<?php
/**
 * MangaPress
 *
 * @package Manga_Press
 * @subpackage Manga_Press_Templates\Single_Comic
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

get_header('comic');
/**
 * mangapress_before_content
 *
 * Run scripts or insert content before the main content area
 * @since 4.0.0
 */
do_action('mangapress_before_content'); ?>

<?php if (have_posts()) : ?>
    <?php
    /**
     * mangapress_before_comic_loop
     *
     * Run scripts or insert content before the main loop
     * @since 4.0.0
     */
    do_action('mangapress_before_comic_loop'); ?>

    <?php while (have_posts()) :
        the_post(); ?>
        <?php
        /**
         * mangapress_before_article
         *
         * Run scripts or insert content before the article tag but after the loop starts
         * @since 4.0.0
         */
        do_action('mangapress_before_article') ?>
        <article <?php post_class() ?>>
            <header class="mangapress_comic_title">
                <h2><?php the_title(); ?></h2>
            </header>
            <?php
            /**
             * mangapress_before_article_content
             *
             * Run scripts or insert content before the article content but after the article opening tag
             * @since 4.0.0
             */
            do_action('mangapress_before_article_content'); ?>

            <?php the_post_thumbnail(); ?>

            <?php
            /**
             * mangapress_after_article_content
             *
             * Run scripts or insert content after the article content but before the article closing tag
             * @since 4.0.0
             */
            do_action('mangapress_after_article_content');  ?>
        </article>

        <?php mangapress_comic_navigation(); ?>

        <?php
        /**
         * mangapress_after_article
         *
         * Run scripts or insert content after the closing article tag
         * but before the main loop ends or iterates to the next post
         * @since 4.0.0
         */
        do_action('mangapress_after_article') ?>
    <?php endwhile; ?>

    <?php
    /**
     * mangapress_after_comic_loop
     *
     * Run scripts or insert content after the main loop
     * @since 4.0.0
     */
    do_action('mangapress_after_comic_loop'); ?>

<?php endif; ?>

<?php
/**
 * mangapress_after_content
 *
 * Run scripts or insert content after the main content area
 * @since 4.0.0
 */
do_action('mangapress_after_content'); ?>

<?php
/**
 * mangapress_sidebar
 *
 * Possibly insert a sidebar
 * @since 4.0.0
 */
do_action('mangapress_sidebar'); ?>

<?php get_footer('comic');