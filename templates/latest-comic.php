<?php
/**
 * Manga+Press Latest Comic Template
 */

get_header('comic');
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
                <?php the_title(); ?>
            </header>
            <?php the_post_thumbnail(); ?>
            <?php mangapress_comic_navigation(); ?>
        </article>
    <?php endwhile; ?>

    <?php do_action('mangapress_after_latest_comic_loop'); ?>

<?php endif; ?>


<?php do_action('mangapress_after_latest_comic'); ?>

<?php do_action('mangapress_sidebar'); ?>

<?php get_footer('comic');
