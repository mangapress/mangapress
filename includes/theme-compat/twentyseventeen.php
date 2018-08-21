<?php
/**
 * Provide theme compatibility for TwentySeventeen
 */

class MangaPress_TwentySeventeen
{
    public static function init()
    {
        $latest_comic_page_exists = MangaPress_Bootstrap::get_option('basic', 'latestcomic_page');
        add_action('mangapress_before_content', array(__CLASS__, 'mangapress_twentyseventeen_before_content'));
        add_action('mangapress_after_content', array(__CLASS__, 'mangapress_twentyseventeen_after_content'));
        if ($latest_comic_page_exists) {
            add_action('mangapress_before_latest_comic_loop', 'mangapress_start_latest_comic');
            add_action('mangapress_after_latest_comic_loop', 'mangapress_end_latest_comic');
        }
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_styles'));
    }


    /**
     * Add TwentySeventeen content area wrapping markup opening tags
     */
    public static function mangapress_twentyseventeen_before_content()
    {
        echo '<div class="wrap">';
        echo '<div id="primary" class="content-area">';
        echo '<main id="main" class="site-main" role="main">';
    }


    /**
     * TwentySeventeen content area closing tags
     */
    public static function mangapress_twentyseventeen_after_content()
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
    public static function enqueue_styles()
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

MangaPress_TwentySeventeen::init();