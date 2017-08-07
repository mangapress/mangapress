<?php
/**
 * Plugin bootstrap class.
 *
 * @package MangaPress\Plugin\Bootstrap
 * @author Jess Green <jgreen at psy-dreamer.com>
 */
namespace MangaPress\Plugin;

class Bootstrap
{


    /**
     * Options array
     *
     * @var array
     */
    protected $options;


    /**
     * Instance of MangaPress_Bootstrap
     *
     * @var Bootstrap
     */
    protected static $instance;


    /**
     * MangaPress Posts object
     *
     * @var \MangaPress\Plugin\Posts
     */
    protected $posts_helper;


    /**
     * Admin page helper
     *
     * @var \MangaPress\Plugin\Admin
     */
    protected $admin_helper;


    /**
     * Flash Message helper
     *
     * @var \MangaPress\Plugin\FlashMessages
     */
    protected $flashmessages_helper;


    /**
     * Static function used to initialize Bootstrap
     *
     * @return void
     */
    public static function load_plugin()
    {
        self::$instance  = new self();
    }


    /**
     * Get instance of MangaPress_Bootstrap
     *
     * @return \MangaPress\Plugin\Bootstrap
     */
    public static function get_instance()
    {
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * PHP5 constructor method
     */
    protected function __construct()
    {
        load_plugin_textdomain(MP_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages');

        add_action('init', array($this, 'init'), 500);
        add_action('widgets_init', array($this, 'widgets_init'));
    }


    /**
     * Run init functionality
     *
     * @see init() hook
     * @return void
     */
    public function init()
    {
        $this->set_options();
        Options::init();
        $this->posts_helper   = new Posts();
        $this->admin_helper   = new Admin();
        $this->flashmessages_helper = new FlashMessages(array(
            'transient_name' => 'mangapress_messages'
        ));


        $this->load_current_options();

        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        add_filter('single_template', 'mangapress_single_comic_template');
        add_filter('template_include', 'mangapress_latestcomic_page_template');
        add_filter('template_include', 'mangapress_comicarchive_page_template');

        if (get_option('mangapress_upgrade') == 'yes') {
            Install::get_instance()->do_upgrade();
        }
    }


    /**
     * Register widgets
     */
    public function widgets_init()
    {
        register_widget('MangaPress\WidgetCalendar');
    }


    /**
     * Get a MangaPress helper
     *
     * @param string $helper_name Allowed values: admin, options, posts, flashmessages
     * @return \MangaPress\Plugin\Admin|\MangaPress\Plugin\Options|\MangaPress\Plugin\Posts|\MangaPress\Plugin\FlashMessages|\WP_Error
     */
    public function get_helper($helper_name)
    {
        $helper = "{$helper_name}_helper";
        if (property_exists($this, $helper)) {
            return $this->$helper;
        }

        return new \WP_Error('_mangapress_helper_access', 'No helper exists by that name');
    }


    /**
     * Set MangaPress options. This method should run every time
     * MangaPress options are updated.
     *
     * @uses init()
     * @see \MangaPress\Plugin\Bootstrap::init()
     *
     * @return void
     */
    public function set_options()
    {
        $this->options = maybe_unserialize(get_option('mangapress_options'));
    }


    /**
     * Get MangaPress options
     *
     * @return array
     */
    public function get_options()
    {
        return $this->options;
    }


    /**
     * Get one option from options array
     *
     * @param string $section Option section
     * @param string $option_name Option name
     * @return boolean|mixed
     */
    public function get_option($section, $option_name)
    {
        if (!isset($this->options[$section][$option_name])) {
            return false;
        }

        return $this->options[$section][$option_name];
    }


    /**
     * Load current plugin options
     *
     * @return void
     */
    private function load_current_options()
    {
        add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));

        $generate_comic_page = $this->get_option('comic_page', 'generate_comic_page');
        if ($generate_comic_page){
            $page_width = $this->get_option('comic_page', 'comic_page_width');
            $page_height = $this->get_option('comic_page', 'comic_page_height');
            add_image_size(
                'comic-page',
                $page_width,
                $page_height,
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
     */
    public function wp_enqueue_scripts()
    {
        wp_register_style(
            'mangapress-nav',
            MP_URLPATH . 'assets/css/nav.css',
            null,
            MP_VERSION,
            'screen'
        );
        wp_register_script(
            'mangapress-lightbox',
            MP_URLPATH . 'assets/js/lightbox.js',
            array('jquery'),
            MP_VERSION
        );

        $nav_css = $this->get_option('nav', 'nav_css');
        $light_box = $this->get_option('comic_page', 'enable_comic_lightbox');
        if ($nav_css == 'default_css') {
            wp_enqueue_style('mangapress-nav');
        }

        if ($light_box) {
            wp_enqueue_script('mangapress-lightbox');
        }

        wp_register_script(
            'mangapress-bookmark',
            MP_URLPATH . 'assets/js/bookmark.js',
            array('jquery'),
            MP_VERSION,
            true
        );

        $bookmark_styles = apply_filters('mangapress_bookmark_styles', array());
        $bookmark_localization = array(
            'bookmarkCloseLabel' => __('close', MP_DOMAIN),
            'bookmarkNoHistory' => __('No bookmark history available.', MP_DOMAIN),
            'bookmarkTitle' => __('Title', MP_DOMAIN),
            'bookmarkDate' => __('Date', MP_DOMAIN),
        );

        wp_localize_script(
            'mangapress-bookmark',
            strtoupper(MP_DOMAIN),
            array_merge($bookmark_styles, $bookmark_localization)
        );

        wp_enqueue_script('mangapress-bookmark');
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
            MP_URLPATH . 'assets/css/font.css',
            null,
            MP_VERSION,
            'screen'
        );
    }
}
