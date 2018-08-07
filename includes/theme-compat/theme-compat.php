<?php
/**
 * Theme compatibility includes
 */
class MangaPress_ThemeCompatibility
{

    /**
     * Initialize class
     */
    public static function init()
    {
        $theme = get_template();
        switch ($theme) {
            case 'twentyfifteen':
                // @todo Define a compatibility class
            break;

            case 'twentysixteen':
                // @todo Define a compatibility class
            break;

            case 'twentyseventeen' :
                require_once MP_ABSPATH . 'includes/theme-compat/' . $theme . '.php';
            break;

            default:
                /**
                 * mangapress_theme_compatible-$theme
                 *
                 * Allow third-party themes to declare Manga+Press compatibility options.
                 *
                 * @since 4.0.0
                 */
                do_action("mangapress_theme_compatible-{$theme}");
        }
    }
}

MangaPress_ThemeCompatibility::init();