<?php


namespace MangaPress\Theme\Compatible;

use MangaPress\Theme\Interfaces\Theme;
use MangaPress\Theme\Traits\Markup;

/**
 * Class Twentynineteen
 * @package MangaPress\Theme\Compatible
 */
class Twentynineteen implements Theme
{
    use Markup;

    /**
     * Set up all needed hook
     */
    public function init()
    {
        add_action('mangapress_before_content', [$this, 'before_content']);
        add_action('mangapress_after_content', [$this, 'after_content']);
        add_action('mangapress_before_article_content', [$this, 'before_article_content']);
        add_action('mangapress_after_article_content', [$this, 'after_article_content']);
    }

    /**
     * Add markup before loop
     */
    public function before_content()
    {
        echo '<section id="primary" class="content-area">';
        echo '<main id="main" class="site-main">';
    }

    /**
     * Add markup after loop
     */
    public function after_content()
    {
        echo '</main>';
        echo '</section>';
    }

    /**
     * Add markup before the article
     */
    public function before_article_content()
    {
        echo '<div class="entry-content">';
    }

    /**
     * Add markup after the article
     */
    public function after_article_content()
    {
        echo '</div>';
    }
}
