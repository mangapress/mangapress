<?php


namespace MangaPress;

use MangaPress\Options\OptionsGroup;

/**
 * Class Bootstrap
 * @package MangaPress
 */
class Bootstrap
{

    /**
     * Initialize plugin
     */
    public function init()
    {
        add_action('plugins_loaded', [$this, 'load_plugin']);
        add_action('init', [$this, 'plugin_init'], 500);
        add_action('admin_init', [$this, 'admin_init']);
        add_action('widgets_init', [$this, 'widgets_init']);
    }

    /**
     * Load plugin
     */
    public function load_plugin()
    {
        $this->load_textdomain();
        (new Plugin())->init();
    }

    /**
     * Set up basename and load plugin's textdomain
     */
    public function load_textdomain()
    {
        load_plugin_textdomain(
            MP_DOMAIN,
            false,
            dirname(MP_BASENAME) . '/resources/languages'
        );
    }

    /**
     * Handle admin-side initialization
     */
    public function admin_init()
    {
        (new OptionsGroup())->init();
    }

    /**
     * Handle loading of frontend assets
     */
    public function plugin_init()
    {
        // do plugin initialization things here
    }

    /**
     * Initialize associated widgets
     */
    public function widgets_init()
    {
        // initialize Widgets here
    }

    public function theme_compat_init()
    {
        // initialize theme compatibility here
    }
}
