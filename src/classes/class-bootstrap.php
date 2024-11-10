<?php
/**
 * Plugin bootstrap class
 *
 * @package MangaPress
 * @subpackage Bootstrap
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

namespace MangaPress;

/**
 * Plugin bootstrap class.
 */
class Bootstrap {

	/**
	 * Options array
	 *
	 * @var array
	 */
	protected array $options;


	/**
	 * Instance of Bootstrap
	 *
	 * @var Bootstrap|null
	 */
	protected static ?Bootstrap $instance = null;


	/**
	 * MangaPress Posts object
	 *
	 * @var Posts
	 */
	protected Posts $posts_helper;


	/**
	 * Options helper object
	 *
	 * @var Options
	 */
	protected Options $options_helper;


	/**
	 * Admin page helper
	 *
	 * @var Admin
	 */
	protected Admin $admin_helper;

	/**
	 * Static function used to initialize Bootstrap
	 *
	 * @return void
	 */
	public static function load_plugin() {
		self::$instance = new self();
	}


	/**
	 * Get instance of Bootstrap
	 *
	 * @return Bootstrap
	 */
	public static function get_instance(): Bootstrap {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * PHP5 constructor method
	 */
	protected function __construct() {
		load_plugin_textdomain( 'mangapress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		add_action( 'init', array( $this, 'init' ), 500 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}


	/**
	 * Run init functionality
	 *
	 * @see init() hook
	 * @return void
	 */
	public function init() {
		Settings::get_instance()->init();
		Posts::get_instance()->init();
		Admin::get_instance()->init();
		Options::get_instance()->init();

		$this->load_current_options();

		add_action( 'save_post_mangapress_comic', 'mangapress_delete_get_calendar_cache' );
		add_action( 'delete_post', 'mangapress_delete_get_calendar_cache' );
		add_action( 'update_option_start_of_week', 'mangapress_delete_get_calendar_cache' );
		add_action( 'update_option_gmt_offset', 'mangapress_delete_get_calendar_cache' );

		add_filter( 'template_include', 'mangapress_single_comic_template' );
		add_filter( 'template_include', 'mangapress_latestcomic_page_template' );
		add_filter( 'template_include', 'mangapress_comicarchive_page_template' );

		if ( get_option( 'mangapress_upgrade' ) === 'yes' ) {
			Install::get_instance()->do_upgrade();
		}
	}


	/**
	 * Register widgets
	 */
	public function widgets_init() {
		register_widget( 'MangaPress\Widget_Calendar' );
	}

	/**
	 * Load current plugin options
	 *
	 * @return void
	 */
	private function load_current_options() {
		/**
		 * Enqueue Default Navigation CSS
		 */
		if ( 'default_css' === Settings::get_option( 'nav', 'nav_css' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_nav_assets' ) );
		}

		/**
		 * Enqueue Lightbox assets
		 */
		if ( Settings::get_option( 'comic_page', 'enable_comic_lightbox' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_lightbox_assets' ) );
			add_action( 'wp_footer', 'mangapress_add_lightbox_markup' );
			add_filter( 'mangapress_comic_image', 'mangapress_add_lightbox_anchor' );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_lightbox_assets' ) );
		}

		if ( Settings::get_option( 'comic_page', 'enable_comic_bookmark' ) ) {
			add_action('wp_enqueue_scripts', array( $this, 'enqueue_bookmark_assets' ) );
			add_shortcode( 'bookmark_comic', 'mangapress_bookmark_button_shortcode' );
			add_filter( 'mangapress_bookmark_styles', 'mangapress_bookmark_styles' );
		}

		/*
		 * Comic Page size
		 */
		if ( Settings::get_option('comic_page', 'generate_comic_page' ) ) {
			add_image_size(
				'comic-page',
				Settings::get_option( 'comic_page', 'comic_page_width' ),
				Settings::get_option('comic_page', 'comic_page_height' ),
				false
			);
		}

		if ( Settings::get_option( 'comic_page', 'enable_opengraph_tags' ) ) {
			add_action( 'wp_head', 'mangapress_add_opengraph_tags', 5 );
		}

		/*
		 * Comic Thumbnail size for Comics Listing screen
		 */
		add_image_size( 'comic-admin-thumb', 60, 80, true );
	}

	/**
	 * Enqueue default navigation stylesheet
	 *
	 * @return void
	 */
	public function enqueue_nav_assets() {
		/*
		 * Navigation style
		 */
		wp_register_style(
			'mangapress-nav',
			MP_URLPATH . 'assets/css/nav.css',
			null,
			MP_VERSION,
			'screen'
		);
		wp_enqueue_style( 'mangapress-nav' );
	}

	/**
	 * Enqueue assets for lightbox
	 *
	 * @return void
	 */
	public function enqueue_lightbox_assets() {
		wp_register_style(
			'mangapress-lightbox',
			MP_URLPATH . 'assets/css/lightbox.css',
			null,
			MP_VERSION,
			'screen'
		);

		wp_register_script(
			'mangapress-lightbox',
			MP_URLPATH . 'assets/js/lightbox.js',
			array( 'jquery' ),
			MP_VERSION
		);

		wp_enqueue_script( 'mangapress-lightbox' );
		wp_enqueue_style( 'mangapress-lightbox' );
	}

	/**
	 * Enqueue assets for bookmark feature
	 *
	 * @return void
	 */
	public function enqueue_bookmark_assets() {

		wp_register_script(
			'mangapress-bookmark',
			MP_URLPATH . 'assets/js/bookmark.js',
			array( 'jquery' ),
			MP_VERSION,
			true
		);

		$bookmark_styles       = apply_filters( 'mangapress_bookmark_styles', array() );
		$bookmark_localization = array(
			'bookmarkCloseLabel' => __( 'close', 'mangapress' ),
			'bookmarkNoHistory'  => __( 'No bookmark history available.', 'mangapress' ),
			'bookmarkTitle'      => __( 'Title', 'mangapress' ),
			'bookmarkDate'       => __( 'Date', 'mangapress' ),
		);

		wp_localize_script(
			'mangapress-bookmark',
			'MANGAPRESS',
			array_merge( $bookmark_styles, $bookmark_localization )
		);

		wp_enqueue_script( 'mangapress-bookmark' );
	}
}
