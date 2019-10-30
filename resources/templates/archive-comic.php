<?php
/**
 * MangaPress
 *
 * @package Manga_Press
 * @subpackage Manga_Press_Templates\Archive_Comic
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

get_header('comic');

/** This filter is documented in templates/single-comic.php */
do_action('mangapress_before_content'); ?>
    <header class="mangapress-comic-archives-header entry-header">
        <h1 class="manga-press-comic-archives-header__title entry-title">
            <?php _e('Comic Archives', MP_DOMAIN); ?>
        </h1>
    </header>
<?php
/**
 * mangapress_before_archive_comic_loop
 *
 * Run scripts or insert content directly before latest comic loop
 * @since 4.0.0
 */
do_action('mangapress_before_archive_comic_loop');

/**
 * mangapress_archive_style_opening_tag
 *
 * Output the opening wrapping tag based on the archive style
 * @param string $archive_style
 * @since 4.0.0
 *
 */
do_action('mangapress_archive_style_opening_tag', mangapress_get_comic_archive_style()); ?>

<?php if (have_posts()) {
    if ((comic_archive_is_gallery() || comic_archive_is_list())) {
        while (have_posts()) :
            the_post();

            /**
             * mangapress_before_article
             *
             * Run scripts or insert content before the article tag but after the loop starts
             * @since 4.0.0
             */
            do_action('mangapress_before_article');

            /**
             * mangapress_opening_article_tag
             *
             * Filter and then output the article tag
             * @param string $archive_style
             * @param array $args {
             *      Array of accepted arguments
             * @type string $style
             * }
             * @since 4.0.0
             *
             */
            echo apply_filters(
                'mangapress_opening_article_tag',
                'article',
                ['style' => mangapress_get_comic_archive_style()]
            );

            /**
             * mangapress_before_article_content
             *
             * Run scripts or insert content before the article content but after the article opening tag
             * @since 4.0.0
             */
            do_action('mangapress_before_article_content');

            /**
             * mangapress_archive_style_template
             *
             * Output the individual archive entry markup based on archive style
             * @param string $archive_style
             * @since 4.0.0
             *
             */
            do_action('mangapress_archive_style_template', mangapress_get_comic_archive_style());

            /**
             * mangapress_after_article_content
             *
             * Run scripts or insert content after the article content but before the article closing tag
             * @since 4.0.0
             */
            do_action('mangapress_after_article_content');

            /**
             * mangapress_closing_article_tag
             *
             * Filter and then output the closing article tag
             * @param string $archive_style
             * @param array $args {
             *      Array of accepted arguments
             * @type string $style
             * }
             * @since 4.0.0
             *
             */
            echo apply_filters(
                'mangapress_closing_article_tag',
                'article',
                ['style' => mangapress_get_comic_archive_style()]
            );

            /**
             * mangapress_after_article
             *
             * Run scripts or insert content after the closing article tag
             * but before the main loop ends or iterates to the next post
             * @since 4.0.0
             */
            do_action('mangapress_after_article');

        endwhile;
    } else {
        // here
    };
} else {
// something this way goes
}

/**
 * mangapress_archive_style_closing_tag
 *
 * Output the closing wrapping tag based on the archive style
 * @param string $archive_style
 * @since 4.0.0
 *
 */
do_action('mangapress_archive_style_closing_tag', mangapress_get_comic_archive_style());

/**
 * mangapress_after_archive_comic_loop
 *
 * Run scripts or insert content directly after comic archive loop
 * @since 4.0.0
 */
do_action('mangapress_after_archive_comic_loop'); ?>

<?php
/** This filter is documented in templates/single-comic.php */
do_action('mangapress_after_content'); ?>

<?php
/** This filter is documented in templates/single-comic.php */
do_action('mangapress_sidebar'); ?>

<?php get_footer('comic');