<?php
/**
 * Manga+Press Latest Comic Template
 *
 * @package Manga_Press
 * @subpackage Manga_Press_Templates\Latest_Comic
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */


/** This filter is documented in templates/single-comic.php */
do_action('mangapress_get_comic_header');

/** This filter is documented in templates/single-comic.php */
do_action('mangapress_before_content');

/**
 * mangapress_latest_comic_header
 *
 * Output page header
 * @since 4.0.0
 */
do_action('mangapress_latest_comic_header');

/**
 * mangapress_before_latest_comic
 *
 * Run scripts or insert content before latest comic loop conditional
 * @since 4.0.0
 */
do_action('mangapress_before_latest_comic');

if (have_posts()) :

    /**
     * mangapress_before_latest_comic_loop
     *
     * Run scripts or insert content directly before latest comic loop
     * @since 4.0.0
     */
    do_action('mangapress_before_latest_comic_loop');

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
            ['style' => mangapress_get_comic_archive_style()]
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

        /** Output comic image */
        the_post_thumbnail();

        /**
         * mangapress_after_article_content
         *
         * Run scripts or insert content after the article content but before the article closing tag
         * @since 4.0.0
         */
        do_action('mangapress_after_article_content');

        /** This filter is documented in resources/templates/archive-comic.php **/
        echo apply_filters(
            'mangapress_closing_article_tag',
            'article',
            ['style' => mangapress_get_comic_archive_style()]
        );

        /** Output navigation */
        mangapress_comic_navigation();

        /**
         * mangapress_article_footer
         *
         * Output article footer
         * @since 4.0.0
         */
        do_action('mangapress_article_footer');

        /**
         * mangapress_after_article
         *
         * Run scripts or insert content after the closing article tag
         * but before the main loop ends or iterates to the next post
         * @since 4.0.0
         */
        do_action('mangapress_after_article');
    endwhile;

    /**
     * mangapress_after_latest_comic_loop
     *
     * Run scripts or insert content directly after latest comic loop
     * @since 4.0.0
     */
    do_action('mangapress_after_latest_comic_loop');

endif;

/**
 * mangapress_after_latest_comic
 *
 * Run scripts or insert content after latest comic loop conditional
 * @since 4.0.0
 */
do_action('mangapress_after_latest_comic');

/** This filter is documented in templates/single-comic.php */
do_action('mangapress_after_content');

/** This filter is documented in templates/single-comic.php */
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
