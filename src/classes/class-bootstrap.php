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

		$enable_opengraph_tags = Settings::get_option( 'comic_page', 'enable_opengraph_tags' );
		if ( $enable_opengraph_tags ) {
			add_action( 'wp_head', 'mangapress_add_opengraph_tags', 5 );
		}
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
		$mp_options = Settings::get_options();

		/*
		 * Disable/Enable Default Navigation CSS
		 */
		if ( 'default_css' === $mp_options['nav']['nav_css'] ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		}

		/*
		 * Comic Page size
		 */
		if ( $mp_options['comic_page']['generate_comic_page'] ) {
			add_image_size(
				'comic-page',
				$mp_options['comic_page']['comic_page_width'],
				$mp_options['comic_page']['comic_page_height'],
				false
			);
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
	public function wp_enqueue_scripts() {
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
}
