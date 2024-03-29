<?php
/**
 * MangaPress Installation Class
 *
 * @package MangaPress
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

namespace MangaPress;

/**
 * MangaPress Installation Class
 *
 * @package MangaPress
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
class Install {



	/**
	 * Current MangaPress DB version
	 *
	 * @var string
	 */
	protected static string $version;


	/**
	 * What type is the object? Activation, deactivation or upgrade?
	 *
	 * @var string
	 */
	protected string $type;


	/**
	 * Instance of Bootstrap class
	 *
	 * @var \Bootstrap
	 */
	protected Bootstrap $bootstrap;


	/**
	 * Instance of Install
	 *
	 * @var \Install|null
	 */
	protected static ?Install $instance = null;


	/**
	 * Get instance of
	 *
	 * @return Install
	 */
	public static function get_instance(): Install {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Static function for plugin activation.
	 *
	 * @return void
	 */
	public function do_activate() {
		global $wp_version;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Check for capability.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_die( esc_html__( 'Sorry, you do not have sufficient permissions to activate this plugin.', 'mangapress' ) );
		}

		// Get the capabilities for the administrator.
		$role = get_role( 'administrator' );

		// Must have admin privileges in order to activate.
		if ( empty( $role ) ) {
			wp_die( esc_html__( 'Sorry, you must be an Administrator in order to use Manga+Press', 'mangapress' ) );
		}

		if ( version_compare( $wp_version, '3.0', '<=' ) ) {
			wp_die(
				esc_html__( 'Sorry, only WordPress 3.0 and later are supported. Please upgrade to WordPress 3.0', 'mangapress' ),
				'Wrong Version'
			);
		}

		self::$version = strval( get_option( 'mangapress_ver' ) );

		// version_compare will still evaluate against an empty string
		// so we have to tell it not to.
		if ( version_compare( self::$version, MP_VERSION, '<' ) && ! ( '' === self::$version ) ) {
			add_option( 'mangapress_upgrade', 'yes', '', 'no' );
		} elseif ( '' === self::$version ) {
			add_option( 'mangapress_ver', MP_VERSION, '', 'no' );
			add_option( 'mangapress_options', Options::get_default_options(), '', 'no' );
		}

		$this->bootstrap = Bootstrap::get_instance();
		$this->bootstrap->init();
		$this->after_plugin_activation();

		flush_rewrite_rules( false );
	}


	/**
	 * Run routines after plugin has been activated
	 *
	 * @return void
	 */
	public function after_plugin_activation() {
		/**
		 * Filter mangapress_after_plugin_activation.
		 * Allow other plugins to add to Manga+Press' activation sequence.
		 *
		 * @return void
		 */
		do_action( 'mangapress_after_plugin_activation' );

		// if the option already exists, exit.
		if ( get_option( 'mangapress_default_category' ) ) {
			return;
		}

		// create a default series category.
		$term = wp_insert_term(
			'Default Series',
			Posts::TAX_SERIES,
			array(
				'description' => __( 'Default Series category created when plugin is activated. It is suggested that you rename this category.', 'mangapress' ),
				'slug'        => 'default-series',
			)
		);

		if ( ! ( $term instanceof WP_Error ) ) {
			add_option( 'mangapress_default_category', $term['term_id'], '', 'no' );
		}
	}


	/**
	 * Static function for plugin deactivation.
	 *
	 * @return void
	 */
	public function do_deactivate() {
		delete_option( 'rewrite_rules' );
		flush_rewrite_rules( false );
	}

	/**
	 * Static function for upgrade
	 *
	 * @return void
	 */
	public function do_upgrade() {
		update_option( 'mangapress_ver', MP_VERSION );
		delete_option( 'mangapress_upgrade' );
		flush_rewrite_rules( false );
	}
}
