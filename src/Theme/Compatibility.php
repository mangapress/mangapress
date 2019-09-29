<?php


namespace MangaPress\Theme;

use MangaPress\Component;
use MangaPress\Theme\Interfaces\Theme;

/**
 * Class Compatibility
 * @package MangaPress\Theme
 */
class Compatibility implements Component
{
    /**
     * Determine if there's compatibility out of the box
     */
    public function init()
    {
        // get current theme name
        // theme names should have dashes and no underscores
        $theme       = get_template();

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
            if ($theme_class instanceof Theme) {
                (new $theme_class())->init();
            }
        }
    }
}
