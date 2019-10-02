<?php


namespace MangaPress\Theme\Traits;

use MangaPress\Options\Options;

/**
 * Trait Markup
 * @package MangaPress\Theme\Traits
 */
trait Markup
{

    /**
     * Markup constructor.
     * Run default settings when object is created
     */
    public function __construct()
    {
        $latest_comic_page_exists = Options::get_option('latestcomic_page', 'basic');
        if ($latest_comic_page_exists) {
            add_action('mangapress_before_latest_comic_loop', 'mangapress_start_latest_comic');
            add_action('mangapress_after_latest_comic_loop', 'mangapress_end_latest_comic');
        }
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
    }

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
