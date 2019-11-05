<?php
/**
 * MangaPress
 *
 * @package Manga_Press
 * @subpackage Manga_Press_Templates\Page_Comic
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

/**
 * @var \WP_Post $post
 */
global $post;

/** This action is documented in resources/templates/single-comic.php **/
do_action('mangapress_get_comic_header');

/**
 * mangapress_before_content
 *
 * Run scripts or insert content before the main content area
 * @since 4.0.0
 */
do_action('mangapress_before_content');

if (have_posts()) :
    /**
     * mangapress_before_comic_loop
     *
     * Run scripts or insert content before the main loop
     * @since 4.0.0
     */
    do_action('mangapress_before_comic_loop');

    while (have_posts()) :
        the_post();

        /**
         * mangapress_before_article
         *
         * Run scripts or insert content before the article tag but after the loop starts
         * @since 4.0.0
         */
        do_action('mangapress_before_article');

        /**
         * mangapress_opening_article_tag
         *
         * Filter and then output the opening article tag
         * @param string $the_content Original post content
         * @param string $tag The content wrapper tag
         * @param array $args {
         * Array of accepted arguments
         * @type string|array $class Wrapper tag's CSS class
         * }
         * @since 4.0.0
         *
         */
        echo apply_filters(
            'mangapress_opening_article_tag',
            'article',
            ['style' => mangapress_get_comic_archive_style()]
        );
        ?>
        <header class="entry-header mangapress_comic_title">
            <h2><?php the_title(); ?></h2>
        </header>
        <?php
        /**
         * mangapress_before_article_content
         *
         * Run scripts or insert content before the article content but after the article opening tag
         * @since 4.0.0
         */
        do_action('mangapress_before_article_content');

        /**
         * mangapress_the_comic_page_content
         *
         * Filter and then output the closing article tag
         * @param string $the_content Original post content
         * @param string $tag The content wrapper tag
         * @param array $args {
         * Array of accepted arguments
         * @type string|array $class Wrapper tag's CSS class
         * }
         * @since 4.0.0
         *
         */
        echo apply_filters(
            'mangapress_the_comic_page_content',
            get_the_content(),
            'div',
            ['class' => ['entry-content']]
        );

        /**
         * mangapress_after_article_content
         *
         * Run scripts or insert content after the article content but before the article closing tag
         * @since 4.0.0
         */
        do_action('mangapress_after_article_content', $post);

        /**
         * mangapress_closing_article_tag
         *
         * Filter and then output the closing article tag
         * @param string $the_content Original post content
         * @param string $tag The content wrapper tag
         * @param array $args {
         * Array of accepted arguments
         * @type string|array $class Wrapper tag's CSS class
         * }
         * @since 4.0.0
         *
         */
        echo apply_filters(
            'mangapress_closing_article_tag',
            'article',
            ['style' => mangapress_get_comic_archive_style()]
        );

        /**
         * mangapress_after_article
         *
         * Run scripts or insert content after the closing article tag
         * but before the main loop ends or iterates to the next post
         * @since 4.0.0
         */
        do_action('mangapress_after_article', $post);
    endwhile;

    /**
     * mangapress_after_comic_loop
     *
     * Run scripts or insert content after the main loop
     * @since 4.0.0
     */
    do_action('mangapress_after_comic_loop');

endif;

/**
 * mangapress_after_content
 *
 * Run scripts or insert content after the main content area
 * @since 4.0.0
 */
do_action('mangapress_after_content');

/**
 * mangapress_sidebar
 *
 * Possibly insert a sidebar
 * @since 4.0.0
 */
do_action('mangapress_sidebar');

/** This action is documented in resources/templates/single-comic.php **/
do_action('mangapress_get_comic_footer');