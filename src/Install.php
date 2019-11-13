<?php
/**
 * @package MangaPress
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress;

use MangaPress\Options\Options;
use MangaPress\Options\OptionsGroup;
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

        if (version_compare(PHP_VERSION, '7.0', '<')) {
            wp_die(
                __(
                    'Sorry. Manga+Press is only supported on PHP 7.0 and newer. '
                    . 'Please upgrade your server\'s PHP version, or contact your hosting provider.',
                    MP_DOMAIN
                )
            );
        }

        if (version_compare($wp_version, '5.2.4', '<')) {
            wp_die(
                'Sorry, only WordPress 5.2.4 and later are supported. Please upgrade to WordPress 5.2.4',
                'Wrong Version'
            );
        }

        $version = strval(get_option('mangapress_ver'));

        // version_compare will still evaluate against an empty string
        // so we have to tell it not to.
        if (version_compare($version, MP_VERSION, '<') && !($version == '')) {
            $this->do_upgrade();
        } elseif ($version == '') {
            add_option('mangapress_ver', MP_VERSION, '', 'no');
            add_option('mangapress_options', serialize(Options::get_options()), '', 'no');
        }

        add_option('mangapress_post_activation', true);

        (new Bootstrap())->init();
    }

    /**
     * Run upgrades
     * @global \wpdb $wpdb
     */
    public function do_upgrade()
    {
        global $wpdb;

        update_option('mangapress_ver', MP_VERSION);

        $options = maybe_unserialize(get_option(OptionsGroup::OPTIONS_GROUP_NAME));

        // switch to using post ids
        $latest  = isset($options['basic']['latestcomic_page']) ? $options['basic']['latestcomic_page'] : false;
        $archive = isset($options['basic']['comicarchive_page']) ? $options['basic']['comicarchive_page'] : false;

        if (!($latest || $archive)) {
            return;
        }

        $raw_sql = "SELECT ID FROM {$wpdb->posts} WHERE post_type='page' "
                   . "AND post_status='publish' "
                   . "AND post_name = %s";

        $latest_id  = $wpdb->get_var($wpdb->prepare($raw_sql, $latest));
        $archive_id = $wpdb->get_var($wpdb->prepare($raw_sql, $archive));

        $options['basic']['latestcomic_page']  = $latest_id;
        $options['basic']['comicarchive_page'] = $archive_id;

        update_option(OptionsGroup::OPTIONS_GROUP_NAME, $options);

        flush_rewrite_rules(false);
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
}
