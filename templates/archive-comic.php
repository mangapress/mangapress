<?php
/**
 * MangaPress
 * @todo Add DocBlocks to custom actions and filters
 * @package Manga_Press
 * @subpackage Manga_Press_Templates\Archive_Comic
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
$archive_style = MangaPress_Bootstrap::get_option('basic', 'comicarchive_page_style');

get_header('comic');

do_action('mangapress_before_content'); ?>
<header class="mangapress-comic-archives-header">
    <h1 class="manga-press-comic-archives-header__title">
        <?php _e('Comic Archives', MP_DOMAIN); ?>
    </h1>
</header>

<?php if (have_posts() && $archive_style !== 'calendar') : // @todo change $archive_style !== 'calendar' to a conditional tag ?>

    <?php do_action('mangapress_before_latest_comic_loop'); ?>

    <?php do_action('mangapress_archive_style_opening_tag', $archive_style); ?>

    <?php while(have_posts()) : the_post(); ?>

        <?php echo apply_filters('mangapress_opening_article_tag', 'article', ['style' => $archive_style]) ?>

        <?php do_action('mangapress_archive_style_template', $archive_style); ?>

        <?php echo apply_filters('mangapress_closing_article_tag', 'article', ['style' => $archive_style]); ?>

    <?php endwhile; ?>

    <?php do_action('mangapress_archive_style_closing_tag', $archive_style); ?>

    <?php do_action('mangapress_after_latest_comic_loop'); ?>
<?php else: ?>
    <?php mangapress_get_archive_style_template('calendar'); ?>
<?php endif; ?>

<?php do_action('mangapress_after_latest_comic'); ?>

<?php do_action('mangapress_after_content'); ?>

<?php do_action('mangapress_sidebar'); ?>

<?php get_footer('comic');