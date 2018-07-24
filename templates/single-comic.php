<?php
/**
 * MangaPress
 *
 * @package Manga_Press
 * @subpackage Manga_Press_Templates\Single_Comic
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

get_header('comic');
/**
 * mangapress_before_content
 *
 * Run scripts or insert content before the main content area
 * @since 3.5.0
 */
do_action('mangapress_before_content'); ?>

<?php if (have_posts()) : ?>

    <?php
    /**
     * mangapress_before_comic_loop
     *
     * Run scripts or insert content before the main loop
     * @since 3.5.0
     */
    do_action('mangapress_before_comic_loop'); ?>

    <?php while(have_posts()) : the_post(); ?>
        <article <?php post_class() ?>>
            <header class="mangapress_comic_title">
                <h2><?php the_title(); ?></h2>
            </header>
            <?php the_post_thumbnail(); ?>
        </article>
        <?php mangapress_comic_navigation(); ?>
    <?php endwhile; ?>

    <?php
    /**
     * mangapress_after_comic_loop
     *
     * Run scripts or insert content after the main loop
     * @since 3.5.0
     */
    do_action('mangapress_after_comic_loop'); ?>

<?php endif; ?>

<?php
/**
 * mangapress_after_content
 *
 * Run scripts or insert content after the main content area
 * @since 3.5.0
 */
do_action('mangapress_after_content'); ?>

<?php
/**
 * mangapress_sidebar
 *
 * Possibly insert a sidebar
 * @since 3.5.0
 */
do_action('mangapress_sidebar'); ?>

<?php get_footer('comic');