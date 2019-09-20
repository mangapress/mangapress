<?php
namespace MangaPress\Lib\ThemeCompat;

/**
 * Theme compatibility includes
 * @todo Switch to registry/service-provider pattern
 */
class ThemeCompatibility
{

    /**
     * Initialize class
     */
    public static function init()
    {        
        require_once MP_ABSPATH . 'includes/lib/theme-compat/ThemeMarkup.php';
        
        $theme = get_template();
        $theme_compat_path = implode('/', ['includes', 'lib', 'theme-compat']) . '/';
        $theme_compat_file = MP_ABSPATH . $theme_compat_path . $theme . '.php';

        if ( ! file_exists( $theme_compat_file )) {
            /**
             * mangapress_theme_compatible-$theme
             *
             * Allow third-party themes to declare Manga+Press compatibility options.
             *
             * @since 4.0.0
             */            
            do_action("mangapress_theme_compatible-{$theme}");
        } else {
            require_once $theme_compat_file;
        }
    }
}