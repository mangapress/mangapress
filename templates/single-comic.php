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

do_action('mangapress_before_content'); ?>

<?php if (have_posts()) : ?>

    <?php do_action('mangapress_before_latest_comic_loop'); ?>

    <?php while(have_posts()) : the_post(); ?>
        <article <?php post_class() ?>>
            <header class="mangapress_comic_title">
                <h2><?php the_title(); ?></h2>
            </header>
            <?php the_post_thumbnail(); ?>
        </article>
        <?php mangapress_comic_navigation(); ?>
    <?php endwhile; ?>

    <?php do_action('mangapress_after_latest_comic_loop'); ?>

<?php endif; ?>

<?php do_action('mangapress_after_latest_comic'); ?>

<?php do_action('mangapress_after_content'); ?>

<?php do_action('mangapress_sidebar'); ?>

<?php get_footer('comic');