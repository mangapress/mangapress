<?php
/**
 * Manga+Press Template functions
 *
 * @todo Update docblocks
 *
 * @package Manga_Press
 * @subpackage Manga_Press_Template_Functions
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

/**
 * @param string $template
 *
 * @return string mixed
 */
function mangapress_template_loader($template)
{
    global $wp_query;
    if ( is_embed() ) {
        return $template;
    }

    $default = mangapress_get_default_template_file();
    if ( $default ) {

        $templates = mangapress_get_template_hierarchy($default);
        $template = locate_template($templates);

        if ( ! $template ) {
            $template = MP_ABSPATH . 'templates/' . $default;
        }
    }

    return $template;
}


/**
 * Get Manga+Press default templates
 *
 * @return string
 */
function mangapress_get_default_template_file()
{
    global $wp_query;

    if (is_singular(MangaPress_Posts::POST_TYPE)) {
        $template = 'single-comic.php';
    } elseif (is_post_type_archive(MangaPress_Posts::POST_TYPE) || is_comic_archive_page()) {
        $template = 'archive-comic.php';
    } elseif (is_latest_comic_page()) {
        $template = 'latest-comic.php';
    } else {
        $template = '';
    }

    return $template;
}

/**
 * Get Manga+Press template hierarchy
 * @param string $template
 *
 * @return array
 */
function mangapress_get_template_hierarchy($template)
{
    global $wp_query;

    $templates[] = 'mangapress.php';
    if (is_singular(MangaPress_Posts::POST_TYPE)) {
        $object = get_queried_object();
        $name_decoded = urldecode( $object->post_name );
        if ( $name_decoded !== $object->post_name ) {
            $templates[] = "comic/single-comic-{$name_decoded}.php";
            $templates[] = "single-comic-{$name_decoded}.php";
        }
        $templates[] = "comic/single-comic-{$object->post_name}.php";
        $templates[] = "single-comic-{$object->post_name}.php";
        $templates[] = "comic/single-comic.php";
        $templates[] = "single-comic.php";
    }

    if (is_post_type_archive(MangaPress_Posts::POST_TYPE) || is_comic_archive_page()) {
        $templates[] = 'comic/archive-comic.php';
        $templates[] = 'archive-comic.php';
        $templates[] = 'archive.php';
    }

    if (is_latest_comic_page()) {
        // no object to query
        $templates[] = 'comic/latest-comic.php';
        $templates[] = 'latest-comic.php';
    }

    return $templates;
}


/**
 * Modify loop and set up for Manga+Press — used only on Latest Comic
 * @param \WP_Query $query
 */
function mangapress_pre_get_posts(\WP_Query $query)
{
    if (is_latest_comic_page()) {
        $query->set('post_type', MangaPress_Posts::POST_TYPE);
        $query->set('posts_per_page', 1);
    }
}
add_action('pre_get_posts', 'mangapress_pre_get_posts');
