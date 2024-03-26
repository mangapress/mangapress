<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */
/*
Plugin Name: Manga+Press Comic Manager
Plugin URI: http://www.manga-press.com/
Description: Turns WordPress into a full-featured Webcomic Manager. Be sure to visit <a href="http://www.manga-press.com/">Manga+Press</a> for more info.
Version: 3.5.0
Requires PHP: 7.4
Requires at least: 6.4
Author: Jess Green
Author URI: http://www.jesgs.com
Text Domain: mangapress
Domain Path: /languages
*/
/*
 * (c) 2024 Jessica C Green
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
$mangapress_plugin_folder = plugin_basename( __DIR__ );

if ( ! defined( 'MP_VERSION' ) ) {
	define( 'MP_VERSION', '3.5.0' );
}

if ( ! defined( 'MP_FOLDER' ) ) {
	define( 'MP_FOLDER', $mangapress_plugin_folder );
}

if ( ! defined( 'MP_ABSPATH' ) ) {
	define( 'MP_ABSPATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'MP_URLPATH' ) ) {
	define( 'MP_URLPATH', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'MP_LANG' ) ) {
	define( 'MP_LANG', $mangapress_plugin_folder . '/languages' );
}

require_once MP_ABSPATH . 'includes/lib/form/class-element.php';
require_once MP_ABSPATH . 'includes/lib/class-flash-messages.php';
require_once MP_ABSPATH . 'includes/lib/class-content-type.php';
require_once MP_ABSPATH . 'includes/lib/class-post-type.php';
require_once MP_ABSPATH . 'includes/lib/class-taxonomy.php';
require_once MP_ABSPATH . 'includes/lib/class-mp-calendar-widget.php';
require_once MP_ABSPATH . 'includes/functions.php';
require_once MP_ABSPATH . 'includes/template-functions.php';
require_once MP_ABSPATH . 'class-mangapress-install.php';
require_once MP_ABSPATH . 'class-mangapress-admin.php';
require_once MP_ABSPATH . 'class-mangapress-options.php';
require_once MP_ABSPATH . 'class-mangapress-posts.php';

$mangapress_install = MangaPress_Install::get_instance();

register_activation_hook( __FILE__, array( $mangapress_install, 'do_activate' ) );
register_deactivation_hook( __FILE__, array( $mangapress_install, 'do_deactivate' ) );

add_action( 'plugins_loaded', array( 'MangaPress_Bootstrap', 'load_plugin' ) );

/**
 * Plugin bootstrap class.
 *
 * @package MangaPress
 * @subpackage MangaPress_Bootstrap
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class MangaPress_Bootstrap {

	/**
	 * Options array
	 *
	 * @var array
	 */
	protected array $options;


	/**
	 * Instance of MangaPress_Bootstrap
	 *
	 * @var MangaPress_Bootstrap
	 */
	protected static MangaPress_Bootstrap $instance;


	/**
	 * MangaPress Posts object
	 *
	 * @var \MangaPress_Posts
	 */
	protected MangaPress_Posts $posts_helper;


	/**
	 * Options helper object
	 *
	 * @var \MangaPress_Options
	 */
	protected MangaPress_Options $options_helper;


	/**
	 * Admin page helper
	 *
	 * @var MangaPress_Admin
	 */
	protected MangaPress_Admin $admin_helper;


	/**
	 * Flash Message helper
	 *
	 * @var MangaPress_FlashMessages
	 */
	protected MangaPress_FlashMessages $flashmessage_helper;


	/**
	 * Static function used to initialize Bootstrap
	 *
	 * @return void
	 */
	public static function load_plugin() {
		self::$instance = new self();
	}


	/**
	 * Get instance of MangaPress_Bootstrap
	 *
	 * @return MangaPress_Bootstrap
	 */
	public static function get_instance(): MangaPress_Bootstrap {
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
		$this->set_options();

		$this->posts_helper        = new MangaPress_Posts();
		$this->admin_helper        = new MangaPress_Admin();
		$this->options_helper      = new MangaPress_Options();
		$this->flashmessage_helper = new MangaPress_FlashMessages(
			array(
				'transient_name' => 'mangapress_messages',
			)
		);

		$this->load_current_options();

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_filter( 'single_template', 'mangapress_single_comic_template' );
		add_filter( 'template_include', 'mangapress_latestcomic_page_template' );
		add_filter( 'template_include', 'mangapress_comicarchive_page_template' );

		if ( get_option( 'mangapress_upgrade' ) === 'yes' ) {
			MangaPress_Install::get_instance()->do_upgrade();
		}
	}


	/**
	 * Register widgets
	 */
	public function widgets_init() {
		register_widget( 'MangaPress_Widget_Calendar' );
	}


	/**
	 * Get a MangaPress helper
	 *
	 * @param string $helper_name Allowed values: admin, options, posts, flashmessages.
	 * @return \MangaPress_Admin|\MangaPress_Options|\MangaPress_Posts|\MangaPress_FlashMessages|\WP_Error
	 */
	public function get_helper( $helper_name ) {
		$helper = "_{$helper_name}_helper";
		if ( property_exists( $this, $helper ) ) {
			return $this->$helper;
		}

		return new WP_Error( '_mangapress_helper_access', 'No helper exists by that name' );
	}


	/**
	 * Set MangaPress options. This method should run every time
	 * MangaPress options are updated.
	 *
	 * @uses init()
	 * @see MangaPress_Bootstrap::init()
	 *
	 * @return void
	 */
	public function set_options() {
		$this->options = maybe_unserialize( get_option( 'mangapress_options' ) );
	}


	/**
	 * Get MangaPress options
	 *
	 * @return array
	 */
	public function get_options() {
		return $this->options;
	}


	/**
	 * Get one option from options array
	 *
	 * @param string $section Option section.
	 * @param string $option_name Option name.
	 *
	 * @return boolean|mixed
	 */
	public function get_option( string $section, string $option_name ) {
		if ( ! isset( $this->options[ $section ][ $option_name ] ) ) {
			return false;
		}

		return $this->options[ $section ][ $option_name ];
	}


	/**
	 * Load current plugin options
	 *
	 * @return void
	 */
	private function load_current_options() {
		$mp_options = $this->get_options();

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


	/**
	 * Enqueue admin-related styles
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style(
			'mangapress-icons',
			plugins_url( 'assets/css/font.css', __FILE__ ),
			null,
			MP_VERSION,
			'screen'
		);
	}
}
