<?php
/**
 * MangaPress
 *
 * @package MangaPress\Templates\Single_Comic
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

/**
 * mangapress_get_comic_header
 *
 * Output theme header specific to Manga+Press, or normal theme header
 * @since 4.0.0
 */
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

        /** This filter is documented in resources/templates/archive-comic.php **/
        echo apply_filters(
            'mangapress_opening_article_tag',
            'article',
            false
        );

        /**
         * mangapress_article_header
         *
         * Add post header
         * @since 4.0.0
         */
        do_action('mangapress_article_header', $post);

        /**
         * mangapress_before_article_content
         *
         * Run scripts or insert content before the article content but after the article opening tag
         * @since 4.0.0
         */
        do_action('mangapress_before_article_content');

        /** Outputs the post thumbnail */
        the_post_thumbnail();

        /**
         * mangapress_after_article_content
         *
         * Run scripts or insert content after the article content but before the article closing tag
         * @since 4.0.0
         */
        do_action('mangapress_after_article_content');

        /**
         * mangapress_article_footer
         *
         * Output article footer
         * @since 4.0.0
         */
        do_action('mangapress_article_footer');

        /** This filter is documented in resources/templates/archive-comic.php **/
        echo apply_filters(
            'mangapress_closing_article_tag',
            'article',
            false
        );

        mangapress_comic_navigation();

        /**
         * mangapress_after_article
         *
         * Run scripts or insert content after the closing article tag
         * but before the main loop ends or iterates to the next post
         * @since 4.0.0
         */
        do_action('mangapress_after_article');

        /**
         * mangapress_comments_template
         *
         * Load theme's comments template
         * @since 4.0.0
         */
        do_action('mangapress_comments_template');

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

/**
 * mangapress_before_footer
 *
 * Handle output before footer
 * @since 4.0.0
 */
do_action('mangapress_before_footer');

/**
 * mangapress_get_comic_footer
 *
 * Output theme footer specific to Manga+Press, or normal theme footer
 * @since 4.0.0
 */
do_action('mangapress_get_comic_footer');
