<?php
/**
 * Provide theme compatibility for TwentySeventeen
 *
 * @package MangaPress\Theme\Compatible
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\Theme\Compatible;

use MangaPress\Theme\Interfaces\Theme;
use MangaPress\Theme\Traits\Markup;

/**
 * Class Twentyseventeen
 * @package MangaPress\Lib\ThemeCompat
 */
class Twentyseventeen implements Theme
{
    use Markup;

    /**
     * Initialize theme
     */
    public function init()
    {

        add_action('mangapress_head', [$this, 'head']);
        add_action('mangapress_after_body_open', [$this, 'after_body_open']);

        add_action('mangapress_page_header', [$this, 'page_header']);
        add_action('mangapress_after_page_header', [$this, 'after_page_header']);
//        add_action('mangapress_article_header', [$this, 'article_header']);

        add_action('mangapress_before_content', [$this, 'before_content']);
        add_action('mangapress_after_content', [$this, 'after_content']);

        add_filter('body_class', [$this, 'body_classes']);
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
             . __('Skip to content', 'twentyseventeen') .
             '</a>';
    }

    /**
     * Add needed body-classes
     *
     * @param array $classes Default body classes
     *
     * @return array
     */
    public function body_classes($classes)
    {
        if (is_active_sidebar('sidebar-1') && (is_comic_archive_page() || is_latest_comic_page())) {
            $classes[] = 'has-sidebar';
        }

        if (is_comic_archive_page() || is_latest_comic_page()) {
            $classes[] = 'archive';
        }

        return $classes;
    }

    /**
     * Output page header
     */
    public function page_header()
    {
        ?>
        <header id="masthead" class="site-header" role="banner">

            <?php get_template_part('template-parts/header/header', 'image'); ?>

            <?php if (has_nav_menu('top')) : ?>
                <div class="navigation-top">
                    <div class="wrap">
                        <?php get_template_part('template-parts/navigation/navigation', 'top'); ?>
                    </div><!-- .wrap -->
                </div><!-- .navigation-top -->
            <?php endif; ?>

        </header><!-- #masthead -->
        <?php
    }

    /**
     * Output markup directly proceeding page header
     */
    public function after_page_header()
    {
        echo '<div class="site-content-contain">';
        echo '<div id="content" class="site-content">';
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
     * Output article header
     */
    public function article_header()
    {
        echo '<header class="entry-header">';
        the_title('<h1 class="entry-title">', '</h1>');
        echo '</header>';
    }

    /**
     * TwentySeventeen content area closing tags
     */
    public function after_content()
    {
        echo '</main> <!-- /#main -->';
        echo '</div> <!-- /#primary -->';

        get_sidebar();

        echo '</div> <!-- /.wrap -->';
    }
}
