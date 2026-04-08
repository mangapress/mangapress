<?php
/**
 * MangaPress JSON-LD builder and output
 *
 * Adds structured data (JSON-LD) for Comic posts (ComicIssue) and Series (ComicSeries).
 *
 * Opt-in only: use option 'mangapress_jsonld_options' to enable.
 *
 * @package MangaPress
 */

namespace MangaPress;

/**
 * JSON-LD singleton class.
 *
 * Builds and outputs JSON-LD structured data for single comic posts (ComicIssue)
 * and series term archives (ComicSeries).
 */
class JSONLD {
	use Singleton;

	/**
	 * Option name for JSON-LD settings.
	 *
	 * @var string
	 */
	protected $option_name = 'mangapress_jsonld_options';

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'wp_head', array( $this, 'output_jsonld' ), 5 );
	}

	/**
	 * Check whether JSON-LD output is enabled.
	 *
	 * Reads the 'mangapress_jsonld_options' option and passes the result
	 * through the 'mangapress_jsonld_enabled' filter.
	 *
	 * @param \WP_Post|\WP_Term|null $post Context object (post or term).
	 * @return bool
	 */
	public function is_enabled( $post = null ): bool {
		$opts    = get_option( $this->option_name, array() );
		$enabled = ! empty( $opts['enabled'] );
		return (bool) apply_filters( 'mangapress_jsonld_enabled', (bool) $enabled, $post );
	}

	/**
	 * Output JSON-LD script block in wp_head.
	 *
	 * @return void
	 */
	public function output_jsonld(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}

		if ( is_singular( Posts::POST_TYPE ) ) {
			global $post;
			if ( ! $post ) {
				return;
			}
			$data        = $this->get_jsonld_for_post( $post );
			$context_obj = $post;
		} elseif ( is_tax( Posts::TAX_SERIES ) ) {
			$term = get_queried_object();
			if ( ! $term || empty( $term->term_id ) ) {
				return;
			}
			$data        = $this->get_jsonld_for_series( $term );
			$context_obj = $term;
		} else {
			return;
		}

		/**
		 * Filter the JSON-LD data array before encoding.
		 *
		 * @param array                    $data        JSON-LD data.
		 * @param \WP_Post|\WP_Term|null   $context_obj Current post or term.
		 */
		$data = apply_filters( 'mangapress_jsonld_data', $data, $context_obj );

		/**
		 * Action fired before the JSON-LD script tag is output.
		 *
		 * @param array                    $data        JSON-LD data.
		 * @param \WP_Post|\WP_Term|null   $context_obj Current post or term.
		 */
		do_action( 'mangapress_jsonld_before_output', $data, $context_obj );

		$json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		/**
		 * Filter the encoded JSON-LD string.
		 *
		 * @param string                   $json        Encoded JSON string.
		 * @param array                    $data        JSON-LD data array.
		 * @param \WP_Post|\WP_Term|null   $context_obj Current post or term.
		 */
		$json = apply_filters( 'mangapress_jsonld_output', $json, $data, $context_obj );

		if ( ! empty( $json ) ) {
			echo '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD; wp_json_encode handles encoding.
		}

		/**
		 * Action fired after the JSON-LD script tag is output.
		 *
		 * @param array                    $data        JSON-LD data.
		 * @param \WP_Post|\WP_Term|null   $context_obj Current post or term.
		 */
		do_action( 'mangapress_jsonld_after_output', $data, $context_obj );
	}

	/**
	 * Build the JSON-LD data array for a single comic post.
	 *
	 * @param \WP_Post $post Comic post object.
	 * @return array
	 */
	public function get_jsonld_for_post( \WP_Post $post ): array {
		$name        = get_post_meta( $post->ID, '_mp_jsonld_name', true ) ?: get_the_title( $post );
		$description = get_post_meta( $post->ID, '_mp_jsonld_description', true )
			?: ( $post->post_excerpt ?: wp_strip_all_tags( wp_trim_words( $post->post_content, 55 ) ) );
		$image       = $this->get_cover_image( $post );
		$author_name = $this->get_post_author_name( $post );

		$data = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'ComicIssue',
			'name'          => $name,
			'description'   => $description,
			'url'           => get_permalink( $post ),
			'datePublished' => get_the_date( 'c', $post ),
			'author'        => array(
				'@type' => 'Person',
				'name'  => $author_name,
			),
		);

		if ( $image ) {
			if ( ! empty( $image['width'] ) && ! empty( $image['height'] ) ) {
				$data['image'] = array(
					'@type'  => 'ImageObject',
					'url'    => $image['url'],
					'width'  => (int) $image['width'],
					'height' => (int) $image['height'],
				);
			} else {
				$data['image'] = $image['url'];
			}
		}

		$data['publisher'] = $this->build_publisher();

		return $data;
	}

	/**
	 * Build the JSON-LD data array for a series taxonomy term.
	 *
	 * @param \WP_Term $term Series taxonomy term.
	 * @return array
	 */
	public function get_jsonld_for_series( \WP_Term $term ): array {
		$term_id     = $term->term_id;
		$name        = get_term_meta( $term_id, 'mp_jsonld_series_name', true ) ?: $term->name;
		$description = get_term_meta( $term_id, 'mp_jsonld_series_description', true )
			?: term_description( $term_id );

		$image    = null;
		$cover_id = (int) get_term_meta( $term_id, 'mp_jsonld_series_cover_attachment_id', true );
		if ( $cover_id ) {
			$meta = wp_get_attachment_metadata( $cover_id );
			$url  = wp_get_attachment_url( $cover_id );
			if ( $url ) {
				$image = array(
					'url'    => $url,
					'width'  => isset( $meta['width'] ) ? $meta['width'] : null,
					'height' => isset( $meta['height'] ) ? $meta['height'] : null,
				);
			}
		}

		$data = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'ComicSeries',
			'name'        => $name,
			'description' => $description,
			'url'         => get_term_link( $term ),
		);

		if ( $image ) {
			if ( ! empty( $image['width'] ) && ! empty( $image['height'] ) ) {
				$data['image'] = array(
					'@type'  => 'ImageObject',
					'url'    => $image['url'],
					'width'  => (int) $image['width'],
					'height' => (int) $image['height'],
				);
			} else {
				$data['image'] = $image['url'];
			}
		}

		$data['publisher'] = $this->build_publisher();

		return $data;
	}

	/**
	 * Retrieve cover image data for the given object.
	 *
	 * Falls back to: explicit meta attachment → featured image → site icon →
	 * publisher logo option.
	 *
	 * @param \WP_Post|null $obj Post object or null for site-level fallback.
	 * @return array{url: string, width: int|null, height: int|null}|null
	 */
	public function get_cover_image( $obj = null ): ?array {
		$attachment_id = null;

		if ( $obj instanceof \WP_Post ) {
			$attachment_id = (int) get_post_meta( $obj->ID, '_mp_jsonld_cover_attachment_id', true );
			if ( ! $attachment_id ) {
				$attachment_id = (int) get_post_thumbnail_id( $obj->ID );
			}
		}

		if ( $attachment_id ) {
			$meta = wp_get_attachment_metadata( $attachment_id );
			$url  = wp_get_attachment_url( $attachment_id );
			if ( $url ) {
				return array(
					'url'    => $url,
					'width'  => isset( $meta['width'] ) ? $meta['width'] : null,
					'height' => isset( $meta['height'] ) ? $meta['height'] : null,
				);
			}
		}

		if ( function_exists( 'get_site_icon_url' ) ) {
			$url = get_site_icon_url();
			if ( $url ) {
				return array( 'url' => $url );
			}
		}

		$opts = get_option( $this->option_name, array() );
		if ( ! empty( $opts['publisher_logo_attachment_id'] ) ) {
			$pid  = (int) $opts['publisher_logo_attachment_id'];
			$meta = wp_get_attachment_metadata( $pid );
			$url  = wp_get_attachment_url( $pid );
			if ( $url ) {
				return array(
					'url'    => $url,
					'width'  => isset( $meta['width'] ) ? $meta['width'] : null,
					'height' => isset( $meta['height'] ) ? $meta['height'] : null,
				);
			}
		}

		return null;
	}

	/**
	 * Build the publisher Organization data structure.
	 *
	 * @return array
	 */
	protected function build_publisher(): array {
		$site_name = get_bloginfo( 'name' );
		$publisher = array(
			'@type' => 'Organization',
			'name'  => $site_name,
		);

		$image = $this->get_cover_image( null );
		if ( $image ) {
			if ( ! empty( $image['width'] ) && ! empty( $image['height'] ) ) {
				$publisher['logo'] = array(
					'@type'  => 'ImageObject',
					'url'    => $image['url'],
					'width'  => (int) $image['width'],
					'height' => (int) $image['height'],
				);
			} else {
				$publisher['logo'] = $image['url'];
			}
		}

		return $publisher;
	}

	/**
	 * Get the display name of the post author.
	 *
	 * Falls back to: explicit meta → WordPress user display name.
	 *
	 * @param \WP_Post $post Post object.
	 * @return string
	 */
	protected function get_post_author_name( \WP_Post $post ): string {
		$author = get_post_meta( $post->ID, '_mp_jsonld_author_name', true );
		if ( $author ) {
			return (string) $author;
		}

		$author_id    = (int) $post->post_author;
		$display_name = get_the_author_meta( 'display_name', $author_id );
		return $display_name ?: '';
	}
}
