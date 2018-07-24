<?php
/**
 * MangaPress
 *
 * @package Manga_Press
 * @subpackage Manga_Press_Templates\Archive_Comic
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
$archive_style = MangaPress_Bootstrap::get_option('basic', 'comicarchive_page_style');

get_header('comic');

/** This filter is documented in templates/single-comic.php */
do_action('mangapress_before_content'); ?>
<header class="mangapress-comic-archives-header">
    <h1 class="manga-press-comic-archives-header__title">
        <?php _e('Comic Archives', MP_DOMAIN); ?>
    </h1>
</header>

<?php if (have_posts() && $archive_style !== 'calendar') : // @todo change $archive_style !== 'calendar' to a conditional tag ?>

    <?php
    /**
     * mangapress_before_archive_comic_loop
     *
     * Run scripts or insert content directly before latest comic loop
     * @since 3.5.0
     */
    do_action('mangapress_before_archive_comic_loop'); ?>

    <?php
    /**
     * mangapress_archive_style_opening_tag
     *
     * Output the opening wrapping tag based on the archive style
     * @since 3.5.0
     *
     * @param string $archive_style
     */
    do_action('mangapress_archive_style_opening_tag', $archive_style); ?>

    <?php while(have_posts()) : the_post(); ?>

        <?php
        /**
         * mangapress_opening_article_tag
         *
         * Filter and then output the article tag
         * @since 3.5.0
         *
         * @param string $archive_style
         * @param array $args {
         *      Array of accepted arguments
         *      @type string $style
         * }
         */
        echo apply_filters('mangapress_opening_article_tag', 'article', ['style' => $archive_style]) ?>

        <?php
        /**
         * mangapress_archive_style_template
         *
         * Output the individual archive entry markup based on archive style
         * @since 3.5.0
         *
         * @param string $archive_style
         */
        do_action('mangapress_archive_style_template', $archive_style); ?>

        <?php
        /**
         * mangapress_closing_article_tag
         *
         * Filter and then output the closing article tag
         * @since 3.5.0
         *
         * @param string $archive_style
         * @param array $args {
         *      Array of accepted arguments
         *      @type string $style
         * }
         */
        echo apply_filters('mangapress_closing_article_tag', 'article', ['style' => $archive_style]); ?>

    <?php endwhile; ?>

    <?php
    /**
     * mangapress_archive_style_closing_tag
     *
     * Output the closing wrapping tag based on the archive style
     * @since 3.5.0
     *
     * @param string $archive_style
     */
    do_action('mangapress_archive_style_closing_tag', $archive_style); ?>

    <?php
    /**
     * mangapress_after_archive_comic_loop
     *
     * Run scripts or insert content directly after comic archive loop
     * @since 3.5.0
     */
    do_action('mangapress_after_archive_comic_loop'); ?>
<?php else: ?>

    <?php mangapress_get_archive_style_template('calendar'); ?>

<?php endif; ?>

<?php
/** This filter is documented in templates/single-comic.php */
do_action('mangapress_after_content'); ?>

<?php
/** This filter is documented in templates/single-comic.php */
do_action('mangapress_sidebar'); ?>

<?php get_footer('comic');