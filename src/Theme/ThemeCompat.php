<?php


namespace MangaPress\Theme;

use MangaPress\PluginComponent;
use MangaPress\Theme\Interfaces\Theme;

use function MangaPress\Theme\Functions\theme_init;

/**
 * Class ThemeCompat
 * @package MangaPress\Theme
 */
class ThemeCompat implements PluginComponent
{
    /**
     * Determine if there's compatibility out of the box
     */
    public function init()
    {
        add_action('after_setup_theme', [$this, 'after_setup_theme']);
    }

    /**
     * Run functionality for setting up themes
     */
    public function after_setup_theme()
    {
        $this->setup_theme();
        theme_init();
    }

    /**
     * Run theme compatibility
     */
    public function setup_theme()
    {
        // get current theme name
        // theme names should have dashes and no underscores
        $theme = get_template();

        // needs to have FQN to work properly
        $theme_class = '\MangaPress\Theme\Compatible\\' . str_replace('-', '', ucwords($theme, '-'));

        if (! class_exists($theme_class)) {
            /**
             * mangapress_theme_compatible-$theme
             *
             * Allow third-party themes to declare Manga+Press compatibility options.
             *
             * @since 4.0.0
             */
            do_action("mangapress_theme_compatible-{$theme}");
        } else {
            /**
             * @var Theme $theme_class
             */
            $theme_compat = new $theme_class;
            if ($theme_compat instanceof Theme) {
                $theme_compat->init();
            }
        }
    }
}
