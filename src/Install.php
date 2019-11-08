<?php
/**
 * @package MangaPress
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress;

use MangaPress\Posts\ComicPages;
use MangaPress\Posts\Comics;

/**
 * Class Install
 * @package MangaPress
 */
class Install
{

    /**
     * @var Install
     */
    protected static $instance;

    /**
     * Current MangaPress DB version
     *
     * @var string
     */
    protected $version;

    /**
     * What type is the object? Activation, deactivation or upgrade?
     *
     * @var string
     */
    protected $type;

    /**
     * Instance of Bootstrap class
     *
     * @var Bootstrap
     */
    protected $bootstrap;

    /**
     * Get instance of
     *
     * @return Install
     */
    public static function get_instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Static function for plugin activation.
     *
     * @return void
     */
    public function do_activate()
    {
        global $wp_version;

        include_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Check for capability
        if (!current_user_can('activate_plugins')) {
            wp_die(__('Sorry, you do not have sufficient permissions to activate this plugin.', MP_DOMAIN));
        }

        // Get the capabilities for the administrator
        $role = get_role('administrator');

        // Must have admin privileges in order to activate.
        if (empty($role)) {
            wp_die(__('Sorry, you must be an Administrator in order to use Manga+Press', MP_DOMAIN));
        }

        if (version_compare(PHP_VERSION, '7.0', '<=')) {
            wp_die(
                __(
                    'Sorry. Manga+Press is only supported on PHP 7.0 and newer. '
                    . 'Please upgrade your server PHP version.',
                    MP_DOMAIN
                )
            );
        }

        if (version_compare($wp_version, '4.9.6', '<=')) {
            wp_die(
                'Sorry, only WordPress 4.9.6 and later are supported. Please upgrade to WordPress 4.9.6',
                'Wrong Version'
            );
        }

        $plugin = new Plugin();

        $version = strval(get_option('mangapress_ver'));

        // version_compare will still evaluate against an empty string
        // so we have to tell it not to.
        if (version_compare($version, MP_VERSION, '<') && !($version == '')) {
            add_option('mangapress_upgrade', 'yes', '', 'no');
        } elseif ($version == '') {
            add_option('mangapress_ver', MP_VERSION, '', 'no');
            add_option('mangapress_options', serialize(Options::get_default_options()), '', 'no');
        }

        unset($plugin);

        (new Bootstrap())->init();

        $this->after_plugin_activation();

        flush_rewrite_rules(false);
    }

    /**
     * Run routines after plugin has been activated
     *
     * @return void
     * @todo   check for existing terms in Series
     */
    public function after_plugin_activation()
    {
        /**
         * mangapress_after_plugin_activation
         * Allow other plugins to add to Manga+Press' activation sequence.
         *
         * @return void
         */
        do_action('mangapress_after_plugin_activation');


        // if the option already exists, exit
        if (!get_option('mangapress_default_category')) {
            // create a default series category
            $term = wp_insert_term(
                'Default Series',
                Comics::TAX_SERIES,
                [
                    'description' => __(
                        'Default Series category created when plugin is activated. '
                        . 'It is suggested that you rename this category.',
                        MP_DOMAIN
                    ),
                    'slug'        => 'default-series',
                ]
            );

            if (!($term instanceof \WP_Error)) {
                add_option('mangapress_default_category', $term['term_id'], '', 'no');
            }
        }

        if (!get_option('mangapress_archive_page')) {
            // create latest comic and comic archive posts
            $archives = wp_insert_post(
                [
                    'post_type'   => ComicPages::POST_TYPE,
                    'post_title'  => 'Comic Archives',
                    'post_name'   => 'comic-archives',
                    'post_status' => 'draft',
                    'meta_input'  => [
                        'comic_page__type' => 'archive',
                    ],
                ]
            );

            if (!($archives instanceof \WP_Error)) {
                add_option('mangapress_archive_page', $archives, '', 'no');
            }
        }

        if (!get_option('mangapress_latest_page')) {
            $latest = wp_insert_post(
                [
                    'post_type'   => ComicPages::POST_TYPE,
                    'post_title'  => 'Latest Comic',
                    'post_name'   => 'latest-comic',
                    'post_status' => 'draft',
                    [
                        'meta_input' => [
                            'comic_page__type' => 'latest',
                        ],
                    ],
                ]
            );

            if (!($latest instanceof \WP_Error)) {
                add_option('mangapress_latest_page', $latest, '', 'no');
            }
        }
    }

    /**
     * Static function for plugin deactivation.
     *
     * @return void
     */
    public function do_deactivate()
    {
        delete_option('rewrite_rules');
        flush_rewrite_rules(false);
    }

    /**
     * Static function for upgrade
     *
     * @return void
     */
    public function do_upgrade()
    {
        update_option('mangapress_ver', MP_VERSION);
        delete_option('mangapress_upgrade');

        flush_rewrite_rules(false);
    }
}
