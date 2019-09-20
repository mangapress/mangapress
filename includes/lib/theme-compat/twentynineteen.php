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
        add_action('mangapress_before_content', [$this, 'before_content']);
        add_action('mangapress_after_content', [$this, 'after_content']);

        add_action('mangapress_before_article_content', [$this, 'before_article_content']);
        add_action('mangapress_after_article_content', [$this, 'after_article_content']);

        if ($latest_comic_page_exists) {
            add_action('mangapress_before_latest_comic_loop', 'mangapress_start_latest_comic');
            add_action('mangapress_after_latest_comic_loop', 'mangapress_end_latest_comic');
        }
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    public function before_content()
    {
        echo '<section id="primary" class="content-area">';
        echo '<main id="main" class="site-main">';
    }

    public function after_content()
    {
        echo '</main>';
        echo '</section>';
    }

    public function before_article_content()
    {
        echo '<div class="entry-content">';
    }

    public function after_article_content()
    {
        echo '</div>';
    }
}

// @todo replace with registry/service-provider pattern
new TwentyNineteen();