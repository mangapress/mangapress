<?php


namespace MangaPress;

use MangaPress\Admin\Admin;
use MangaPress\Options\Options;
use MangaPress\Options\OptionsGroup;
use MangaPress\Posts\Comics;
use MangaPress\Theme\ThemeCompat;
use MangaPress\PluginComponent;
use PhpOption\Option;

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
        add_filter(
            'plugin_action_links_' . MP_BASENAME,
            [$this, 'plugin_action_links'],
            10,
            4
        );

        add_filter('plugin_row_meta', [$this, 'plugin_row_meta'], 10, 4);
        add_action('display_post_states', [$this, 'display_post_states'], 20, 2);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
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

        $latest  = Options::get_option('latestcomic_page', 'basic');
        $archive = Options::get_option('comicarchive_page', 'basic');
        if ($latest === $post->post_name) {
            $post_states[] = 'Latest Comic Page';
        }

        if ($archive === $post->post_name) {
            $post_states[] = 'Comic Archive Page';
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
            '<a href=" % 1$s" aria-label=" % 2$s">%2$s</a>',
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

        add_action('admin_notices', [$this, 'notice_archive_page'], 555);
        add_action('admin_notices', [$this, 'notice_latest_page'], 555);
        add_action('admin_notices', [$this, 'notice_front_posts_page'], 555);
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
        $page_slug = get_post_field('post_name', $post);
        $post_type = get_post_type($post);

        if ($post_type !== 'page') {
            return false;
        }

        $archive_page = Options::get_option('comicarchive_page', 'basic');

        if ($page_slug == $archive_page) {
            if (get_current_screen()->is_block_editor()) {
                wp_enqueue_script('wp-notices');

                wp_add_inline_script(
                    'wp-notices',
                    sprintf(
                        'wp.data.dispatch( "core / notices" )' .
                        '.createWarningNotice( " % s", { actions: [ %s ], isDismissible: false } )',
                        __('You are currently editing the page that shows your archived comics.', MP_DOMAIN),
                        false
                    ),
                    'after'
                );
            } else {
                echo '<div class="notice notice - warning inline"><p>'
                     . __('You are currently editing the page that shows your archived comics.', MP_DOMAIN)
                     . '</p></div>';
                remove_post_type_support($post_type, 'editor');
            }
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
        $page_slug = get_post_field('post_name', $post);
        $post_type = get_post_type($post);

        if ($post_type !== 'page') {
            return false;
        }

        $latest_page = Options::get_option('latestcomic_page', 'basic');

        if ($page_slug == $latest_page) {
            if (get_current_screen()->is_block_editor()) {
                wp_enqueue_script('wp-notices');

                wp_add_inline_script(
                    'wp-notices',
                    sprintf(
                        'wp.data.dispatch( "core / notices" )' .
                        '.createWarningNotice( " % s", { actions: [ %s ], isDismissible: false } )',
                        __('You are currently editing the page that shows your latest comics.', MP_DOMAIN),
                        false
                    ),
                    'after'
                );
            } else {
                echo '<div class="notice notice - warning inline"><p>'
                     . __('You are currently editing the page that shows your latest comics.', MP_DOMAIN)
                     . '</p></div>';
                remove_post_type_support($post_type, 'editor');
            }
        }
    }

    /**
     * Warning for Front/Home Page
     */
    public function notice_front_posts_page()
    {
        $post_id = intval(filter_input(INPUT_GET, 'post'));
        if (!$post_id) {
            return false;
        }

        $page_for_posts = get_option('page_for_posts', false);
        $page_on_front  = get_option('page_on_front', false);

        if (in_array($post_id, [$page_for_posts, $page_on_front])) {
            if (get_current_screen()->is_block_editor()) {
                wp_enqueue_script('wp-notices');

                wp_add_inline_script(
                    'wp-notices',
                    sprintf(
                        'wp.data.dispatch( "core / notices" )' .
                        '.createWarningNotice( " % s", { actions: [ %s ], isDismissible: false } )',
                        __(
                            'Either assign Latest/Comic archive to different pages, '
                            . 'or assign Home Page/Post Page to different pages.',
                            MP_DOMAIN
                        ),
                        wp_json_encode(
                            [
                                'url'   => admin_url('options-reading.php'),
                                'label' => __('Reading Settings'),
                            ]
                        )
                    ),
                    'after'
                );

                wp_add_inline_script(
                    'wp-notices',
                    sprintf(
                        'wp.data.dispatch( "core / notices" )' .
                        '.createWarningNotice( " % s", { actions: [ %s ], isDismissible: false } )',
                        __(
                            'You have assigned this page to be the Home Page or the Posts page. '
                            . 'This option is not compatible with Manga+Press and will break the functionality '
                            . 'of these two pages.',
                            MP_DOMAIN
                        ),
                        false
                    ),
                    'after'
                );
            } else {
                echo '<div class="notice notice - error inline">';
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
}
