<?php


namespace MangaPress;

use MangaPress\Admin\Admin;
use MangaPress\Options\OptionsGroup;
use MangaPress\Posts\Comics;
use MangaPress\Theme\Compatibility;
use MangaPress\Theme\Shortcode;
use MangaPress\Theme\TemplateLoader;
use MangaPress\Widgets\WidgetsRegistry;

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
    }

    /**
     * Load plugin
     */
    public function load_plugin()
    {
        $this->load_textdomain();

        (new Plugin())
            ->init()
            ->load_components([
                Comics::class,
                Compatibility::class,
                Shortcode::class,
                OptionsGroup::class,
                Admin::class,
                WidgetsRegistry::class,
                Compatibility::class,
                Shortcode::class,
                TemplateLoader::class,
            ]);
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
}
