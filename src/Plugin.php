<?php


namespace MangaPress;

use MangaPress\Admin\Admin;
use MangaPress\Options\OptionsGroup;
use MangaPress\Posts\Comics;
use MangaPress\Theme\Compatibility;
use MangaPress\Component;

/**
 * Class Plugin
 * @package MangaPress
 */
class Plugin implements Component
{

    /**
     * @var array $plugin_data
     */
    protected $plugin_data = [];

    /**
     * @var OptionsGroup $options_group
     */
    protected $options_group;

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
        if (!function_exists('get_plugin_data')) {
            require ABSPATH . '/wp-admin/includes/plugin.php';
        }

        $this->plugin_data = get_plugin_data(MP_ABSPATH);

        // define some other constants
        define('MP_VERSION', $this->plugin_data['Version']);
    }

    /**
     * Init
     */
    public function init()
    {
        $this->options_group = new OptionsGroup();

        add_filter(
            'plugin_action_links_' . MP_BASENAME,
            [$this, 'plugin_action_links'],
            10,
            4
        );

        add_filter('plugin_row_meta', [$this, 'plugin_row_meta'], 10, 4);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('current_screen', [$this, 'add_edit_page_warnings']);

//        add_action('admin_menu', [$this, 'admin_menu']);
//        add_action('admin_init', [$this, 'admin_init']);

        return $this;
    }

    /**
     * Load plugin components
     * @param array $components
     */
    public function load_components($components = [])
    {
        /**
         * @var \MangaPress\Component $component
         */
        foreach ($components as $component) {
            $comp = new $component;
            if ($comp instanceof Component) {
                $comp->init();
            }
        }
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
    public function plugin_row_meta($plugin_meta, $plugin_file, $plugin_data, $status)
    {
        if ($plugin_file !== MP_BASENAME) {
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
    public function plugin_action_links($actions, $plugin_file, $plugin_data, $context)
    {
        $menu_link           = menu_page_url('mangapress-options-page', false);
        $settings            = __('Settings', MP_DOMAIN);
        $actions['settings'] = vsprintf(
            '<a href="%1$s" aria-label="%2$s">%2$s</a>',
            [
                $menu_link,
                $settings,
            ]
        );

        return $actions;
    }

    /**
     * Enqueue admin-related styles
     *
     * @return void
     */
    public function admin_enqueue_scripts()
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
    public function add_edit_page_warnings()
    {
        if (!is_admin()) {
            return;
        }

        add_action('edit_form_after_title', [$this, 'edit_form_after_title_archive_page'], 555);
        add_action('edit_form_after_title', [$this, 'edit_form_after_title_latest_page'], 555);
        add_action('edit_form_after_title', [$this, 'edit_form_after_title_front_posts_page'], 555);
    }

    /**
     * Warning for Comic Archive Page
     */
    public function edit_form_after_title_archive_page()
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
    public function edit_form_after_title_latest_page()
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
    public function edit_form_after_title_front_posts_page()
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
