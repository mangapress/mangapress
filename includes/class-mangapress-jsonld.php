<?php
/**
 * MangaPress JSON-LD builder and output
 *
 * Adds structured data (JSON-LD) for Comic posts (ComicIssue) and Series (ComicSeries).
 *
 * @package MangaPress
 * @subpackage JSONLD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MangaPress JSON-LD builder and output class.
 */
class MangaPress_JSONLD {

	/**
	 * Singleton instance
	 *
	 * @var MangaPress_JSONLD|null
	 */
	protected static $instance;

	/**
	 * Option name for JSON-LD settings
	 *
	 * @var string
	 */
	protected $option_name = 'mangapress_jsonld_options';

	/**
	 * Get instance
	 *
	 * @return MangaPress_JSONLD
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_head', array( $this, 'output_jsonld' ), 5 );
	}

	/**
	 * Is JSON-LD enabled in settings?
	 *
	 * @param mixed $post Optional post or post ID to allow context-aware enabling.
	 * @return bool
	 */
	public function is_enabled( $post = null ) {
		$opts    = get_option( $this->option_name, array() );
		$enabled = ! empty( $opts['enabled'] );
		return (bool) apply_filters( 'mangapress_jsonld_enabled', (bool) $enabled, $post );
	}

	/**
	 * Output JSON-LD in the head when appropriate.
	 *
	 * @return void
	 */
	public function output_jsonld() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$comic_post_type = class_exists( 'MangaPress\Posts' ) ? \MangaPress\Posts::POST_TYPE : 'mangapress_comic';
		$series_tax      = class_exists( 'MangaPress\Posts' ) ? \MangaPress\Posts::TAX_SERIES : 'mangapress_series';

		$post = null;
		$term = null;

		if ( is_singular( $comic_post_type ) ) {
			global $post;
			if ( ! $post ) {
				return;
			}
			$data = $this->get_jsonld_for_post( $post );
		} elseif ( is_tax( $series_tax ) ) {
			$term = get_queried_object();
			if ( ! $term || empty( $term->term_id ) ) {
				return;
			}
			$data = $this->get_jsonld_for_series( $term );
		} else {
			return;
		}

		$context = null !== $post ? $post : $term;

		$data = apply_filters( 'mangapress_jsonld_data', $data, $context );
		do_action( 'mangapress_jsonld_before_output', $data, $context );

		$json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		$json = apply_filters( 'mangapress_jsonld_output', $json, $data, $context );

		if ( ! empty( $json ) ) {
			echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		do_action( 'mangapress_jsonld_after_output', $data, $context );
	}

	/**
	 * Build JSON-LD for a comic post (ComicIssue)
	 *
	 * @param WP_Post $post WordPress post object.
	 * @return array
	 */
	public function get_jsonld_for_post( WP_Post $post ) {
		$name        = get_post_meta( $post->ID, '_mp_jsonld_name', true );
		$name        = $name ? $name : get_the_title( $post );
		$description = get_post_meta( $post->ID, '_mp_jsonld_description', true );
		if ( ! $description ) {
			$description = $post->post_excerpt ? $post->post_excerpt : wp_strip_all_tags( wp_trim_words( $post->post_content, 55 ) );
		}

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
	 * Build JSON-LD for a series term (ComicSeries)
	 *
	 * @param WP_Term $term WordPress term object.
	 * @return array
	 */
	public function get_jsonld_for_series( $term ) {
		$term_id     = $term->term_id;
		$name        = get_term_meta( $term_id, 'mp_jsonld_series_name', true );
		$name        = $name ? $name : $term->name;
		$description = get_term_meta( $term_id, 'mp_jsonld_series_description', true );
		$description = $description ? $description : term_description( $term_id );

		$image    = null;
		$cover_id = get_term_meta( $term_id, 'mp_jsonld_series_cover_attachment_id', true );
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
	 * Get cover image for a post. Returns array with url, width, height when available.
	 *
	 * Priority: post meta _mp_jsonld_cover_attachment_id -> post thumbnail ->
	 * publisher_logo_attachment_id option -> site icon.
	 *
	 * @param WP_Post|null $obj Post object or null for site-level fallbacks only.
	 * @return array|null
	 */
	public function get_cover_image( $obj = null ) {
		$attachment_id = null;

		if ( $obj instanceof WP_Post ) {
			$attachment_id = get_post_meta( $obj->ID, '_mp_jsonld_cover_attachment_id', true );
			if ( ! $attachment_id ) {
				$attachment_id = get_post_thumbnail_id( $obj->ID );
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

		if ( function_exists( 'get_site_icon_url' ) ) {
			$url = get_site_icon_url();
			if ( $url ) {
				return array( 'url' => $url );
			}
		}

		return null;
	}

	/**
	 * Build publisher object for JSON-LD
	 *
	 * @return array
	 */
	protected function build_publisher() {
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
	 * Get post author's display name
	 *
	 * @param WP_Post $post WordPress post object.
	 * @return string
	 */
	protected function get_post_author_name( WP_Post $post ) {
		$author = get_post_meta( $post->ID, '_mp_jsonld_author_name', true );
		if ( $author ) {
			return (string) $author;
		}

		$author_id    = (int) $post->post_author;
		$display_name = get_the_author_meta( 'display_name', $author_id );
		return $display_name ? $display_name : '';
	}
}
