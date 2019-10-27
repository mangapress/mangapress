<?php
/**
 * MangaPress
 *
 * @package Manga_Press
 * @subpackage Manga_Press_Templates\Header_Comic
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */
?>

    <!doctype html>

<html <?php language_attributes(); ?>>
    <head>
        <?php
        /**
         * mangapress_head
         *
         * Insert <head> specific markup before wp_head is run
         * @since 4.0.0
         */
        do_action('mangapress_head');
        ?>
        <?php wp_head(); ?>
    </head>

<body <?php body_class(); ?>>
<?php do_action('wp_body_open'); ?>

<?php
/**
 * mangapress_after_body_open
 *
 * Runs after wp_body_open. Allows for markup insertion following <body> and wp_body_open()
 * @since 4.0.0
 */
do_action('mangapress_after_body_open');

/**
 * mangapress_page_header
 *
 * Allows for site branding/main page header insertion
 * @since 4.0.0
 */
do_action('mangapress_page_header');

/**
 * mangapress_after_page_header
 *
 * Allows for content to be inserted directly after header
 * @since 4.0.0
 */
do_action('mangapress_after_page_header');