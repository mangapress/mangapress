<?php
/**
 * MangaPress Posts class
 * Handles functionality for the Comic post-type
 *
 * @package MangaPress
 * @subpackage MangaPress_Posts
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */

/**
 * MangaPress Posts class
 * Handles functionality for the Comic post-type
 *
 * @package MangaPress
 * @subpackage MangaPress_Posts
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */
class MangaPress_Posts {

	/**
	 * Get image html
	 *
	 * @var string
	 */
	const ACTION_GET_IMAGE_HTML = 'mangapress-get-image-html';


	/**
	 * Remove image html and return Add Image string
	 *
	 * @var string
	 */
	const ACTION_REMOVE_IMAGE = 'mangapress-remove-image';


	/**
	 * Nonce string
	 *
	 * @var string
	 */
	const NONCE_INSERT_COMIC = 'mangapress_comic-insert-comic';


	/**
	 * Post-type name
	 *
	 * @var string
	 */
	const POST_TYPE = 'mangapress_comic';


	/**
	 * Taxonomy name for Series
	 *
	 * @var string
	 */
	const TAX_SERIES = 'mangapress_series';


	/**
	 * Default archive date format
	 *
	 * @var string
	 */
	const COMIC_ARCHIVE_DATEFORMAT = 'm.d.Y';


	/**
	 * Class for initializing custom post-type
	 *
	 * @var MangaPress_PostType
	 */
	private MangaPress_PostType $post_type;


	/**
	 * Post-type Slug. Defaults to comic.
	 *
	 * @var string
	 */
	protected string $slug = 'comic';


	/**
	 * Constructor
	 */
	public function __construct() {
		$this->register_post_type();
		$this->rewrite_rules();

		// Setup Manga+Press Post Options box.
		add_action( 'wp_ajax_' . self::ACTION_GET_IMAGE_HTML, array( $this, 'get_image_html_ajax' ) );
		add_action( 'wp_ajax_' . self::ACTION_REMOVE_IMAGE, array( $this, 'get_image_html_ajax' ) );
		add_action( 'save_post_mangapress_comic', array( $this, 'save_post' ), 500, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/*
		 * Actions and filters for modifying our Edit Comics page.
		 */
		add_action( 'manage_posts_custom_column', array( $this, 'comics_headers' ) );
		add_filter( 'manage_edit-mangapress_comic_columns', array( $this, 'comics_columns' ) );
	}


	/**
	 * Register the post-type
	 *
	 * @return void
	 */
	private function register_post_type() {
		// register taxonomy.
		$taxonomy = new MangaPress_Taxonomy(
			array(
				'name'       => self::TAX_SERIES,
				'textdomain' => 'mangapress',
				'singlename' => __( 'Series', 'mangapress' ),
				'pluralname' => __( 'Series', 'mangapress' ),
				'objects'    => array( 'mangapress_comic' ),
				'arguments'  => array(
					'hierarchical' => true,
					'query_var'    => 'series',
					'rewrite'      => array(
						'slug' => 'series',
					),
				),
			)
		);

		$this->post_type = new MangaPress_PostType(
			array(
				'name'       => self::POST_TYPE,
				'textdomain' => 'mangapress',
				'pluralname' => __( 'Comics', 'mangapress' ),
				'singlename' => __( 'Comic', 'mangapress' ),
				'arguments'  => array(
					'supports'             => array(
						'title',
						'comments',
						'thumbnails',
						'publicize',
					),
					'show_in_rest'         => true,
					'register_meta_box_cb' => array( $this, 'meta_box_cb' ),
					'menu_icon'            => null,
					'rest_namespace'       => 'mangapress/v1',
					'rewrite'              => array(
						'slug' => $this->get_slug(),
					),
					'taxonomies'           => array(
						$taxonomy->get_name(),
					),
				),
			)
		);
	}


	/**
	 * Add new rewrite rules for Comic post-type
	 */
	private function rewrite_rules() {
		$post_type = self::POST_TYPE;
		$slug      = $this->get_slug();

		add_rewrite_rule(
			"{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$",
			'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]&post_type=' . $post_type,
			'top'
		);

		add_rewrite_rule(
			"{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$",
			'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]&post_type=' . $post_type,
			'top'
		);

		add_rewrite_rule(
			"{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$",
			'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]&post_type=' . $post_type,
			'top'
		);

		add_rewrite_rule(
			"{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$",
			'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&post_type=' . $post_type,
			'top'
		);

		add_rewrite_rule(
			"{$slug}/([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$",
			'index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]&post_type=' . $post_type,
			'top'
		);

		add_rewrite_rule(
			"{$slug}/([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$",
			'index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]&post_type=' . $post_type,
			'top'
		);

		add_rewrite_rule(
			"{$slug}/([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$",
			'index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]&post_type=' . $post_type,
			'top'
		);

		add_rewrite_rule(
			"{$slug}/([0-9]{4})/([0-9]{1,2})/?$",
			'index.php?year=$matches[1]&monthnum=$matches[2]&post_type=' . $post_type,
			'top'
		);

		add_rewrite_rule(
			"{$slug}/([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$",
			'index.php?year=$matches[1]&feed=$matches[2]&post_type=' . $post_type,
			'top'
		);

		add_rewrite_rule(
			"{$slug}/([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$",
			'index.php?year=$matches[1]&feed=$matches[2]&post_type=' . $post_type,
			'top'
		);

		add_rewrite_rule(
			"{$slug}/([0-9]{4})/page/?([0-9]{1,})/?$",
			'index.php?year=$matches[1]&paged=$matches[2]&post_type=' . $post_type,
			'top'
		);

		add_rewrite_rule(
			"{$slug}/([0-9]{4})/?$",
			'index.php?year=$matches[1]&post_type=' . $post_type,
			'top'
		);
	}


	/**
	 * Get current user-specified front-slug for Comic archives
	 *
	 * @return string
	 */
	public function get_slug() {
		/**
		 * Filter mangapress_comic_front_slug
		 * Allow plugins (or options) to modify post-type front slug
		 *
		 * @param string $slug Default post-type slug
		 * @return string
		 */
		return apply_filters( 'mangapress_comic_front_slug', $this->slug );
	}


	/**
	 * Meta box call-back function.
	 *
	 * @return void
	 */
	public function meta_box_cb() {
		add_meta_box(
			'comic-image',
			__( 'Comic Image', 'mangapress' ),
			array( $this, 'comic_meta_box_cb' ),
			$this->post_type->get_name(),
			'normal',
			'high'
		);

		/*
		 * Because we don't need this...the comic image is the "Featured Image"
		 */
		remove_meta_box( 'postimagediv', 'mangapress_comic', 'side' );
	}


	/**
	 * Comic meta box
	 *
	 * @return void
	 */
	public function comic_meta_box_cb() {
		require_once MP_ABSPATH . 'includes/pages/meta-box-add-comic.php';
	}


	/**
	 * Enqueue scripts for post-edit and post-add screens
	 *
	 * @global WP_Post $post
	 * @return void
	 */
	public function enqueue_scripts() {
		$current_screen = get_current_screen();

		if ( ! isset( $current_screen->post_type ) || ! isset( $current_screen->base ) ) {
			return;
		}

		if ( ! ( self::POST_TYPE === $current_screen->post_type && 'post' === $current_screen->base ) ) {
			return;
		}

		// Include in admin_enqueue_scripts action hook.
		wp_enqueue_media();
		wp_register_script(
			'mangapress-media-popup',
			plugins_url( '/assets/js/add-comic.js', __FILE__ ),
			array( 'jquery' ),
			MP_VERSION,
			true
		);

		wp_localize_script(
			'mangapress-media-popup',
			'mangapress',
			array(
				'title'  => __( 'Upload or Choose Your Comic Image File', 'mangapress' ),
				'button' => __( 'Insert Comic into Post', 'mangapress' ),
			)
		);

		wp_enqueue_script( 'mangapress-media-popup' );
	}


	/**
	 * Modify header columns for Comic Post-type
	 *
	 * @param string $column Screen column name.
	 * @global WP_Post $post
	 * @return void
	 */
	public function comics_headers( string $column ) {
		global $post;

		$allowed_html = wp_kses_allowed_html( 'post' );
		switch ( $column ) {
			case 'cb':
				echo '<input type="checkbox" value="' . esc_attr( $post->ID ) . '" name="post[]" />';
				break;
			case 'thumbnail':
				echo wp_kses( $this->get_thumbnail( $post ), $allowed_html );
				break;
			case 'title':
				echo wp_kses( $post->post_title, $allowed_html );
				break;
			case 'series':
				echo wp_kses( $this->get_series_links( $post ), $allowed_html );
				break;
			case 'post_date':
				echo date( 'Y/m/d', strtotime( $post->post_date ) ); // @phpcs:ignore -- Sanitization is handled via date() function
				break;
			case 'description':
				echo wp_kses( $post->post_excerpt, $allowed_html );
				break;
			case 'author':
				echo wp_kses( $post->post_author, $allowed_html );
				break;
		}
	}


	/**
	 * Modify comic columns for Comics screen
	 *
	 * @param array $columns Screen columns.
	 *
	 * @return array
	 */
	public function comics_columns( array $columns ): array {

		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'thumbnail'   => __( 'Thumbnail', 'mangapress' ),
			'title'       => __( 'Comic Title', 'mangapress' ),
			'series'      => __( 'Series', 'mangapress' ),
			'description' => __( 'Description', 'mangapress' ),
		);

		return $columns;
	}


	/**
	 * Retrieve image HTML
	 *
	 * @return void
	 */
	public function get_image_html_ajax() {
		$image_id = filter_input( INPUT_POST, 'id' ) ?: false;
		$action   = filter_input( INPUT_POST, 'action' ) ?: self::ACTION_REMOVE_IMAGE;

		header( 'Content-type: application/json' );
		if ( self::ACTION_GET_IMAGE_HTML === $action ) {
			if ( $image_id ) {
				echo wp_json_encode( array( 'html' => $this->get_image_html( $image_id ) ) );
			}
		} else {
			echo wp_json_encode( array( 'html' => $this->get_remove_image_html() ) );
		}

		die();
	}


	/**
	 * Retrieve image html
	 *
	 * @param int $image_id Image attachment ID.
	 *
	 * @return string
	 */
	public function get_image_html( int $image_id ): string {
		$mangapress_image_html = wp_get_attachment_image( $image_id, 'medium' );
		if ( '' === $mangapress_image_html ) {
			return '';
		}

		ob_start();
		require_once MP_ABSPATH . 'includes/pages/set-image-link.php';
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}


	/**
	 * Reset comic image html
	 *
	 * @return string
	 */
	public function get_remove_image_html(): string {

		ob_start();
		require_once MP_ABSPATH . 'includes/pages/remove-image-link.php';
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}


	/**
	 * Save post metadata. By default, Manga+Press uses the _thumbnail_id
	 * meta key. This is the same meta key used for the post featured image.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post WordPress Post object.
	 *
	 * @return int
	 */
	public function save_post( int $post_id, WP_Post $post ): int {
		if ( self::POST_TYPE !== $post->post_type || empty( $_POST ) ) {
			return $post_id;
		}

		if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_insert_comic' ), self::NONCE_INSERT_COMIC ) ) {
			return $post_id;
		}

		$image_id = (int) filter_input( INPUT_POST, '_mangapress_comic_image', FILTER_SANITIZE_NUMBER_INT );
		if ( $image_id ) {
			set_post_thumbnail( $post_id, $image_id );
		}

		// if no terms have been assigned, assign the default.
		if ( ! isset( $_POST['tax_input'][ self::TAX_SERIES ][0] ) || ( 0 === $_POST['tax_input'][ self::TAX_SERIES ][0] && 1 === count( $_POST['tax_input'][ self::TAX_SERIES ] ) ) ) {
			$default_cat = get_option( 'mangapress_default_category' );
			wp_set_post_terms( $post_id, $default_cat, self::TAX_SERIES );
		} else {
			// continue as normal.
			wp_set_post_terms( $post_id, $_POST['tax_input'][ self::TAX_SERIES ], self::TAX_SERIES );
		}

		return $post_id;
	}

	/**
	 * Get post thumbnail for column.
	 *
	 * @param WP_Post $post WordPress post object.
	 *
	 * @return string
	 */
	public function get_thumbnail( WP_Post $post ): string {
		$thumbnail_html = get_the_post_thumbnail( $post->ID, 'comic-admin-thumb', array( 'class' => 'wp-caption' ) );

		if ( $thumbnail_html ) {
			$edit_link = get_edit_post_link( $post->ID, 'display' );

			return '<a href="' . esc_url( $edit_link ) . '">' . $thumbnail_html . '</a>';
		} else {
			return 'No image';
		}
	}

	/**
	 * Get series links for screen columns.
	 *
	 * @param WP_Post $post WordPress post object.
	 *
	 * @return string
	 */
	public function get_series_links( WP_Post $post ): string {
		$series = wp_get_object_terms( $post->ID, 'mangapress_series' );
		if ( empty( $series ) ) {
			return '';
		}

		$series_html = array();
		foreach ( $series as $s ) {
			$series_html[] =
				'<a href="' . get_term_link( $s->slug, 'mangapress_series' ) . '">' . esc_textarea( $s->name ) . '</a>';
		}

		return implode( ', ', $series_html );
	}
}
