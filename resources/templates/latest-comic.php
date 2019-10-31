<?php
/**
 * Manga+Press Latest Comic Template
 *
 * @package Manga_Press
 * @subpackage Manga_Press_Templates\Latest_Comic
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

get_header('comic');

/** This filter is documented in templates/single-comic.php */
do_action('mangapress_before_content'); ?>
    <header class="entry-header mangapress-latest-comic-header">
        <h1 class="entry-title manga-press-latest-comic-header__title">
            <?php _e('Latest Comic', MP_DOMAIN); ?>
        </h1>
    </header>

<?php
/**
 * mangapress_before_latest_comic
 *
 * Run scripts or insert content before latest comic loop conditional
 * @since 4.0.0
 */
do_action('mangapress_before_latest_comic'); ?>

<?php if (have_posts()) : ?>
    <?php
    /**
     * mangapress_before_latest_comic_loop
     *
     * Run scripts or insert content directly before latest comic loop
     * @since 4.0.0
     */
    do_action('mangapress_before_latest_comic_loop'); ?>

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
            do_action('mangapress_before_article_content'); ?>

            <?php the_post_thumbnail(); ?>

            <?php
            /**
             * mangapress_after_article_content
             *
             * Run scripts or insert content after the article content but before the article closing tag
             * @since 4.0.0
             */
            do_action('mangapress_after_article_content'); ?>
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
     * mangapress_after_latest_comic_loop
     *
     * Run scripts or insert content directly after latest comic loop
     * @since 4.0.0
     */
    do_action('mangapress_after_latest_comic_loop'); ?>

<?php endif; ?>


<?php
/**
 * mangapress_after_latest_comic
 *
 * Run scripts or insert content after latest comic loop conditional
 * @since 4.0.0
 */
do_action('mangapress_after_latest_comic'); ?>

<?php
/** This filter is documented in templates/single-comic.php */
do_action('mangapress_after_content'); ?>

<?php
/** This filter is documented in templates/single-comic.php */
do_action('mangapress_sidebar'); ?>

<?php get_footer('comic');
