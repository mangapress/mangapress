<?php


namespace MangaPress\Lib\ThemeCompat;


use MangaPress\Bootstrap;

trait ThemeMarkup
{
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

    abstract public static function before_content();
    abstract public static function after_content();

    /**
     * Enqueue Manga+Press-specific stylesheet
     */
    public function enqueue_styles()
    {
        $theme = get_template();

        wp_register_style(
            "mangapress-{$theme}",
            MP_URLPATH . "assets/css/{$theme}.css",
            null,
            MP_VERSION
        );

        wp_enqueue_style("mangapress-{$theme}");
    }
}