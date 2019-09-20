<?php
/**
 * Provide theme compatibility for TwentySeventeen
 */

namespace MangaPress\Lib\ThemeCompat;

use MangaPress\Bootstrap;

class TwentySeventeen
{
    use ThemeMarkup;

    public function init()
    {
        $latest_comic_page_exists = Bootstrap::get_option('basic', 'latestcomic_page');
        add_action('mangapress_before_content', [$this, 'before_content']);
        add_action('mangapress_after_content', [$this, 'after_content']);
        if ($latest_comic_page_exists) {
            add_action('mangapress_before_latest_comic_loop', 'mangapress_start_latest_comic');
            add_action('mangapress_after_latest_comic_loop', 'mangapress_end_latest_comic');
        }
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
    }


    /**
     * Add TwentySeventeen content area wrapping markup opening tags
     */
    public function before_content()
    {
        echo '<div class="wrap">';
        echo '<div id="primary" class="content-area">';
        echo '<main id="main" class="site-main" role="main">';
    }


    /**
     * TwentySeventeen content area closing tags
     */
    public function after_content()
    {
        echo '</main>';
        echo '</div>';
        if (!is_page()) {
            get_sidebar();
        }
        echo '</div>';
    }


    /**
     * Enqueue Manga+Press-specific stylesheet
     */
    public function enqueue_styles()
    {
        wp_register_style(
            'mangapress-twentyseventeen',
            MP_URLPATH . 'assets/css/twentyseventeen.css',
            null,
            MP_VERSION
        );

        wp_enqueue_style('mangapress-twentyseventeen');
    }
}

new TwentySeventeen();