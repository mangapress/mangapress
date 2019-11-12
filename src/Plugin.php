<?php
/**
 * @package MangaPress
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress;

use MangaPress\Options\Options;
use MangaPress\Posts\ComicPages;

/**
 * Class Plugin
 * @package MangaPress
 */
class Plugin implements PluginComponent
{

    /**
     * @var array $plugin_data
     */
    protected $plugin_data = [];

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
//        if (!function_exists('get_plugin_data')) {
//            require ABSPATH . '/wp-admin/includes/plugin.php';
//        }
//
//        $this->plugin_data = get_plugin_data(MP_ABSPATH . 'mangapress.php');
//
//        // define some other constants
//        define('MP_VERSION', $this->plugin_data['Version']);
    }

    /**
     * Init
     */
    public function init()
    {
        add_filter(
            'plugin_action_links_' . MP_BASENAME,
            [$this, 'plugin_action_links'],
            10,
            4
        );

        add_filter('plugin_row_meta', [$this, 'plugin_row_meta'], 10, 4);
        add_action('display_post_states', [$this, 'display_post_states'], 20, 2);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_filter('use_block_editor_for_post_type', [$this, 'gutenberg_can_edit_post_type'], 20, 2);
        add_action('current_screen', [$this, 'add_edit_page_warnings']);
        add_action('init', [$this, 'do_rewrite_flush']);

        return $this;
    }

    /**
     * Load plugin components
     * @param array $components
     */
    public function load_components($components = [])
    {
        /**
         * @var \MangaPress\PluginComponent $component
         */
        foreach ($components as $component) {
            $comp = new $component;
            if ($comp instanceof PluginComponent) {
                $comp->init();
            }
        }
    }

    /**
     * Get for mangapress_flush_rewrite_rules option, if true then flush rules
     */
    public function do_rewrite_flush()
    {
        $do_flush = boolval(get_option('mangapress_flush_rewrite_rules', false));
        if ($do_flush) {
            flush_rewrite_rules();
            delete_option('mangapress_flush_rewrite_rules');
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
     * Set post status for designated pages
     *
     * @param string[] $post_states Array of post states
     * @param \WP_Post $post
     * @return array
     */
    public function display_post_states($post_states, $post)
    {
        if (!is_admin()) {
            return $post_states;
        }

        $latest  = (int)Options::get_option('latestcomic_page', 'basic');
        $archive = (int)Options::get_option('comicarchive_page', 'basic');

        if ($latest === $post->ID) {
            $post_states[] = __('Latest Comic Page', MP_DOMAIN);
        }

        if ($archive === $post->ID) {
            $post_states[] = __('Comic Archive Page', MP_DOMAIN);
        }

        return $post_states;
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
        // stub function
    }

    /**
     * Show a warning on edit screens if the page is selected to be Latest/Comic Archive
     */
    public function add_edit_page_warnings()
    {
        if (!is_admin()) {
            return;
        }

        add_action('admin_notices', [$this, 'notice_archive_page'], 555);
        add_action('admin_notices', [$this, 'notice_latest_page'], 555);
    }

    /**
     * Disable Gutenberg on Archive & Latest only
     *
     * @param boolean $can_edit
     * @param string $post_type
     * @return bool
     * @todo Explore the possibility of adding a setting to turn on the editor
     */
    public function gutenberg_can_edit_post_type($can_edit, $post_type)
    {
        if ($post_type === 'mangapress_comicpage' || $post_type === 'page') {
            $post_id = filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);

            $latest_page  = (int)Options::get_option('latestcomic_page', 'basic');
            $archive_page = (int)Options::get_option('comicarchive_page', 'basic');


            if (in_array($post_id, [$latest_page, $archive_page])) {
                remove_post_type_support($post_type, 'editor');
                return false;
            }
        }

        return $can_edit;
    }

    /**
     * Warning for Comic Archive Page
     */
    public function notice_archive_page()
    {
        $post_id = intval(filter_input(INPUT_GET, 'post'));
        if (!$post_id) {
            return false;
        }

        $post      = get_post($post_id);
        $post_id   = get_post_field('ID', $post);
        $post_type = get_post_type($post);

        if ($post_type !== 'page') {
            return false;
        }

        $archive_page = (int)Options::get_option('comicarchive_page', 'basic');

        if ($post_id == $archive_page) {
            echo '<div class="notice notice-warning inline"><p>'
                 . __('You are currently editing the page that shows your archived comics.', MP_DOMAIN)
                 . '</p></div>';
        }
    }

    /**
     * Warning for Latest Comic Page
     */
    public function notice_latest_page()
    {
        $post_id = intval(filter_input(INPUT_GET, 'post'));
        if (!$post_id) {
            return false;
        }

        $post      = get_post($post_id);
        $post_id   = get_post_field('ID', $post);
        $post_type = get_post_type($post);

        if (!in_array($post_type, ['page', ComicPages::POST_TYPE])) {
            return false;
        }

        $latest_page = (int)Options::get_option('latestcomic_page', 'basic');

        if ($post_id == $latest_page) {
            echo '<div class="notice notice-warning inline"><p>'
                 . __('You are currently editing the page that shows your latest comics.', MP_DOMAIN)
                 . '</p></div>';
        }
    }
}
