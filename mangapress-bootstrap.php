<?php

namespace MangaPress;

use MangaPress\Lib\FlashMessages as FlashMessages;
use MangaPress\Lib\ThemeCompat\ThemeCompatibility;

/**
 * Plugin bootstrap class.
 *
 * @package    MangaPress
 * @subpackage Bootstrap
 * @author     Jess Green <support@manga-press.com>
 */
class Bootstrap
{

    /**
     * Options array
     *
     * @var array
     */
    protected static $options;


    /**
     * MangaPress Posts object
     *
     * @var Posts
     */
    protected static $posts_helper;


    /**
     * Options helper object
     *
     * @var Options
     */
    protected static $options_helper;


    /**
     * Admin page helper
     *
     * @var Admin
     */
    protected static $admin_helper;


    /**
     * Flash Message helper
     *
     * @var FlashMessages
     */
    protected static $flashmessage_helper;


    /**
     * Plugin's basename. Used for setting paths
     *
     * @var string
     */
    protected static $plugin_basename;

    /**
     * Static function used to initialize Bootstrap
     *
     * @return void
     */
    public static function load_plugin()
    {
        self::$plugin_basename = plugin_basename(__FILE__);
        load_plugin_textdomain(
            MP_DOMAIN,
            false,
            dirname(self::$plugin_basename) . '/languages'
        );

        self::set_options();
        self::load_current_options();
        include_once MP_ABSPATH . 'includes/lib/theme-compat/theme-compat.php';

        add_action('init', ['\MangaPress\Bootstrap', 'init'], 500);
        add_action('widgets_init', ['\MangaPress\Bootstrap', 'widgets_init']);
        add_filter(
            'plugin_action_links_' . self::$plugin_basename,
            ['\MangaPress\Bootstrap', 'plugin_action_links'],
            10,
            4
        );
        add_filter('plugin_row_meta', ['\MangaPress\Bootstrap', 'plugin_row_meta'], 10, 4);

        ThemeCompatibility::init();
    }


    /**
     * Add relevant links to plugin meta
     *
     * @param array $plugin_meta An array of the plugin's metadata
     * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
     * @param array $plugin_data An array of plugin data.
     * @param string $status Status of the plugin.
     *
     * @return array
     */
    public static function plugin_row_meta($plugin_meta, $plugin_file, $plugin_data, $status)
    {
        if ($plugin_file !== self::$plugin_basename) {
            return $plugin_meta;
        }

        $details_link = $plugin_meta[2];
        unset($plugin_meta[2]);
        $link_html = '<a aria-label="%1$s" target="_blank" rel="noopener noreferrer" href="%2$s">%1$s</a>';

        $getting_started      = __('Getting Started', MP_DOMAIN);
        $getting_started_link = 'https://docs.manga-press.com/getting-started/';
        $developer_api        = __('Developer API', MP_DOMAIN);
        $developer_api_link   = 'https://docs.manga-press.com/developer-api/';

        $plugin_meta[] = vsprintf($link_html, [$getting_started, $getting_started_link,]);
        $plugin_meta[] = vsprintf($link_html, [$developer_api, $developer_api_link,]);

        $plugin_meta[] = $details_link;
        return $plugin_meta;
    }


    /**
     * Add additional links to action menu
     *
     * @param array $actions An array of plugin action links.
     * @param string $plugin_file Path to the plugin file relative to the plugins directory.
     * @param array $plugin_data An array of plugin data. See `get_plugin_data()`.
     * @param string $context The plugin context. By default this can include 'all', 'active', 'inactive',
     *                            'recently_activated', 'upgrade', 'mustuse', 'dropins', and 'search'.
     *
     * @return array
     */
    public static function plugin_action_links($actions, $plugin_file, $plugin_data, $context)
    {
        $menu_link           = menu_page_url(Admin::ADMIN_PAGE_SLUG, false);
        $settings            = __('Settings', MP_DOMAIN);
        $actions['settings'] = "<a href='{$menu_link}' aria-label='{$settings}'>{$settings}</a>";

        return $actions;
    }


    /**
     * Run init functionality
     *
     * @return void
     * @see    init() hook
     */
    public static function init()
    {
        // check if rewrite rules need to be updated
        $do_flush = boolval(get_option('mangapress_flush_rewrite_rules', false));
        if ($do_flush) {
            flush_rewrite_rules();
            delete_option('mangapress_flush_rewrite_rules');
        }

        Admin::init();
        Options::init();

        self::$posts_helper        = new Posts();
        self::$flashmessage_helper = new FlashMessages(
            [
                'transient_name' => 'mangapress_messages',
            ]
        );

        add_action('admin_enqueue_scripts', ['\MangaPress\Bootstrap', 'admin_enqueue_scripts']);
        add_action('current_screen', ['\MangaPress\Bootstrap', 'add_edit_page_warnings']);

        add_filter('template_include', 'mangapress_template_loader');

        if (get_option('mangapress_upgrade') == 'yes') {
            Install::get_instance()->do_upgrade();
        }
    }


    /**
     * Register widgets
     */
    public static function widgets_init()
    {
        register_widget('MangaPress\Lib\Widget_Calendar');
    }


    /**
     * Get a MangaPress helper
     *
     * @param string $helper_name Allowed values: admin, options, posts, flashmessages
     * @return Admin|Options|Posts|FlashMessages|\WP_Error
     */
    public static function get_helper($helper_name)
    {
        $helper = "{$helper_name}_helper";
        if (property_exists('\MangaPress\Bootstrap', $helper)) {
            return self::${$helper};
        }

        return new \WP_Error('_mangapress_helper_access', 'No helper exists by that name');
    }


    /**
     * Set MangaPress options. This method should run every time
     * MangaPress options are updated.
     *
     * @return void
     * @see    MangaPress_Bootstrap::init()
     *
     * @uses   init()
     */
    public static function set_options()
    {
        self::$options = maybe_unserialize(get_option('mangapress_options'));
    }


    /**
     * Get MangaPress options
     *
     * @return array
     */
    public static function get_options()
    {
        return self::$options;
    }


    /**
     * Get one option from options array
     *
     * @param string $section Option section
     * @param string $option_name Option name
     * @return boolean|mixed
     */
    public static function get_option($section, $option_name)
    {
        if (!isset(self::$options[$section][$option_name])) {
            return false;
        }

        return self::$options[$section][$option_name];
    }


    /**
     * Load current plugin options
     *
     * @return void
     */
    private static function load_current_options()
    {
        $mp_options = self::get_options();

        if ($mp_options['basic']['latestcomic_page']) {
            add_filter('mangapress_latest_comic_slug', [Posts::class, 'set_latest_comic_slug']);
        }

        if ($mp_options['basic']['comicarchive_page']) {
            add_filter('mangapress_comic_archives_slug', [Posts::class, 'set_comic_archives_slug']);
        }

        /*
         * Disable/Enable Default Navigation CSS
         */
        if ($mp_options['nav']['nav_css'] == 'default_css') {
            add_action('wp_enqueue_scripts', ['\MangaPress\Bootstrap', 'wp_enqueue_scripts']);
        }

        /*
         * Comic Page size
         */
        if ($mp_options['comic_page']['generate_comic_page']) {
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
        add_image_size('comic-admin-thumb', 60, 80, true);
    }


    /**
     * Enqueue default navigation stylesheet
     *
     * @return void
     */
    public static function wp_enqueue_scripts()
    {
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

        wp_enqueue_style('mangapress-nav');
    }


    /**
     * Enqueue admin-related styles
     *
     * @return void
     */
    public static function admin_enqueue_scripts()
    {
        wp_enqueue_style(
            'mangapress-icons',
            plugins_url('assets/css/font.css', __FILE__),
            null,
            MP_VERSION,
            'screen'
        );
    }


    /**
     * Show a warning on edit screens if the page is selected to be Latest/Comic Archive
     */
    public static function add_edit_page_warnings()
    {
        if (!is_admin()) {
            return;
        }

        add_action('edit_form_after_title', ['\MangaPress\Bootstrap', 'edit_form_after_title_archive_page'], 555);
        add_action('edit_form_after_title', ['\MangaPress\Bootstrap', 'edit_form_after_title_latest_page'], 555);
        add_action('edit_form_after_title', ['\MangaPress\Bootstrap', 'edit_form_after_title_front_posts_page'], 555);
    }


    /**
     * Warning for Comic Archive Page
     */
    public static function edit_form_after_title_archive_page()
    {
        $post_id = intval(filter_input(INPUT_GET, 'post'));
        if (!$post_id) {
            return false;
        }

        $post         = get_post($post_id);
        $page_slug    = get_post_field('post_name', $post);
        $post_type    = get_post_type($post);
        $archive_page = self::get_option('basic', 'comicarchive_page');

        if ($page_slug == $archive_page) {
            echo '<div class="notice notice-warning inline"><p>'
                 . __('You are currently editing the page that shows your archived comics.', MP_DOMAIN)
                 . '</p></div>';
            remove_post_type_support($post_type, 'editor');
        }
    }


    /**
     * Warning for Comic Archive Page
     */
    public static function edit_form_after_title_latest_page()
    {
        $post_id = intval(filter_input(INPUT_GET, 'post'));
        if (!$post_id) {
            return false;
        }

        $post        = get_post($post_id);
        $page_slug   = get_post_field('post_name', $post);
        $post_type   = get_post_type($post);
        $latest_page = self::get_option('basic', 'latestcomic_page');

        if ($page_slug == $latest_page) {
            echo '<div class="notice notice-warning inline"><p>'
                 . __('You are currently editing the page that shows your latest comics.', MP_DOMAIN)
                 . '</p></div>';
            remove_post_type_support($post_type, 'editor');
        }
    }


    /**
     * Warning for Front/Home Page
     */
    public static function edit_form_after_title_front_posts_page()
    {
        $post_id = intval(filter_input(INPUT_GET, 'post'));
        if (!$post_id) {
            return false;
        }

        $page_for_posts = get_option('page_for_posts', false);
        $page_on_front  = get_option('page_on_front', false);

        if (in_array($post_id, [$page_for_posts, $page_on_front])) {
            echo '<div class="notice notice-error inline">';
            echo '<p>'
                 . __(
                     'You have assigned this page to be the Home Page or the Posts page. '
                     . 'This option is not compatible with Manga+Press and will break the functionality '
                     . 'of these two pages.',
                     MP_DOMAIN
                 )
                 . '</p>';

            echo '<p>'
                 . __(
                     'Either assign Latest/Comic archive to different pages, '
                     . 'or assign Home Page/Post Page to different pages.',
                     MP_DOMAIN
                 )
                 . '</p>';
            echo '</div>';
        }
    }
}
