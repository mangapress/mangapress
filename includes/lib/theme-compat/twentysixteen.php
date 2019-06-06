<?php
/**
 * Provide theme compatibility for TwentySeventeen
 */
namespace MangaPress\Lib\ThemeCompat;
use MangaPress\Bootstrap;

class TwentySixteen
{
    use ThemeMarkup;

    /**
     * Add TwentySixteen content area wrapping markup opening tags
     */
    public static function before_content()
    {
        echo '<div class="wrap">';
        echo '<div id="primary" class="content-area">';
        echo '<main id="main" class="site-main" role="main">';
    }


    /**
     * TwentySixteen content area closing tags
     */
    public static function after_content()
    {
        echo '</main>';
        echo '</div>';

        get_sidebar();

        echo '</div>';
    }
}

TwentySixteen::init();