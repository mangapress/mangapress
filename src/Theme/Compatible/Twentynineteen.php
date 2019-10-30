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
     * Set up all needed hooks
     */
    public function init()
    {
        add_theme_support('mangapress', ['cover-images']);

        add_action('mangapress_head', [$this, 'head']);
        add_action('mangapress_after_body_open', [$this, 'after_body_open']);
        add_action('mangapress_page_header', [$this, 'page_header']);
        add_action('mangapress_after_page_header', [$this, 'after_page_header']);
        add_action('mangapress_before_content', [$this, 'before_content']);
        add_action('mangapress_after_content', [$this, 'after_content']);
        add_action('mangapress_before_article_content', [$this, 'before_article_content']);
        add_action('mangapress_after_article_content', [$this, 'after_article_content']);
        add_action('mangapress_before_archive_comic_loop', [$this, 'before_archive_comic_loop']);
        add_action('mangapress_after_archive_comic_loop', [$this, 'after_archive_comic_loop']);
    }

    /**
     * Add head markup before wp_head is run
     */
    public function head()
    {
        echo '<meta charset="' . get_bloginfo('charset') . '"/>';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1"/>';
        echo '<link rel="profile" href="https://gmpg.org/xfn/11"/>';
    }

    /**
     * Add markup directly after open body tag
     */
    public function after_body_open()
    {
        echo '<div id="page" class="site">';
        echo '<a class="skip-link screen-reader-text" href="#content">'
             . __('Skip to content', 'twentynineteen') .
             '</a>';
    }

    /**
     * TwentyNineteen adds featured image to header. For comic posts, we don't need that.
     * Instead, if TwentyNineteen—add a "Cover Image" field to be used instead
     */
    public function page_header()
    {
        $classes = is_singular() && twentynineteen_can_show_post_thumbnail() && mangapress_has_cover_image()
            ? 'site-header featured-image' : 'site-header';
        ?>
        <header id="masthead" class="<?php echo $classes; ?>">

            <div class="site-branding-container">
                <?php get_template_part('template-parts/header/site', 'branding'); ?>
            </div><!-- .site-branding-container -->

            <?php
            if (is_singular() && mangapress_has_cover_image()) : ?>
                <div class="site-featured-image">
                    <?php
                    mangapress_twentynineteen_comic_cover();
                    the_post();
                    $discussion = !is_page() ? twentynineteen_get_discussion_data() : null;

                    $classes = 'entry-header';
                    if (!empty($discussion) && absint($discussion->responses) > 0) {
                        $classes = 'entry-header has-discussion';
                    }
                    ?>
                    <div class="<?php echo $classes; ?>">
                        <?php get_template_part('template-parts/header/entry', 'header'); ?>
                    </div><!-- .entry-header -->
                    <?php rewind_posts(); ?>
                </div>
            <?php endif; ?>
        </header><!-- #masthead -->
        <?php
    }

    /**
     * Add opening content tag directly after masthead output
     */
    public function after_page_header()
    {
        echo '<div id="content" class="site-content">';
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
        if (!(comic_archive_is_gallery() || comic_archive_is_list())) {
            echo '<div class="entry-content">';
        }
    }

    /**
     * Add markup after the article
     */
    public function after_article_content()
    {
        if (!(comic_archive_is_gallery() || comic_archive_is_list())) {
            echo '</div>';
        }
    }

    /**
     * Add markup before start of loop on archive page
     */
    public function before_archive_comic_loop()
    {
        echo sprintf('<article class="%s">', implode(' ', get_post_class()));
        echo '<div class="entry-content">';
    }

    /**
     * Add markup after end of loop on archive page
     */
    public function after_archive_comic_loop()
    {
        echo '</div>';
        echo '</article>';
    }
}
