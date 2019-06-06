<?php
/**
 * Provide theme compatibility for TwentyNineteen
 */
namespace MangaPress\Lib\ThemeCompat;


use MangaPress\Bootstrap;

class TwentyNineteen
{
    use ThemeMarkup;

    public function init()
    {
        $latest_comic_page_exists = Bootstrap::get_option('basic', 'latestcomic_page');
        add_action('mangapress_before_content', [__CLASS__, 'before_content']);
        add_action('mangapress_after_content', [__CLASS__, 'after_content']);

        add_action('mangapress_before_article_content', [__CLASS__, 'before_article_content']);
        add_action('mangapress_after_article_content', [__CLASS__, 'after_article_content']);

        if ($latest_comic_page_exists) {
            add_action('mangapress_before_latest_comic_loop', 'mangapress_start_latest_comic');
            add_action('mangapress_after_latest_comic_loop', 'mangapress_end_latest_comic');
        }
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_styles']);
    }

    public static function before_content()
    {
        echo '<section id="primary" class="content-area">';
        echo '<main id="main" class="site-main">';
    }

    public static function after_content()
    {
        echo '</main>';
        echo '</section>';
    }

    public static function before_article_content()
    {
        echo '<div class="entry-content">';
    }

    public static function after_article_content()
    {
        echo '</div>';
    }
}

TwentyNineteen::init();