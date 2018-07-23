<?php
/**
 * Manga+Press Latest Comic Template
 * @todo Add DocBlocks to custom actions and filters
 * @package Manga_Press
 * @subpackage Manga_Press_Templates\Latest_Comic
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

get_header('comic');

do_action('mangapress_before_content');
?>
<header class="mangapress-latest-comic-header">
    <h1 class="manga-press-latest-comic-header__title">
        <?php _e('Latest Comic', MP_DOMAIN); ?>
    </h1>
</header>

<?php do_action('mangapress_before_latest_comic'); ?>

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
