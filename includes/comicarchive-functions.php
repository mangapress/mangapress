<?php
/**
 * mangapress
 *
 * @package comicarchive-functions
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */

/**
 * Get all comics for archives page
 *
 * @since 2.9
 * @return \WP_Query
 */
function mangapress_get_all_comics_for_archive($params = array())
{
    global $wp_actions;

    // TODO Ordering options
    // TODO Sort parameters for taxonomies
    // TODO Move add/remove filter calls to separate functions
    do_action('_mangapress_pre_archives_get_posts');

    $mp_options = MangaPress_Bootstrap::get_instance()->get_options();
    $order_params = array(
        'order' => $mp_options['basic']['archive_order'],
        'orderby' => $mp_options['basic']['archive_orderby'],
    );

    if (empty($params)) {
        $params = array(
            'post_type'      => 'mangapress_comic',
            'posts_per_page' => -1,
            'post_status'    => 'publish'
        );
    }
    $params = array_merge($params, $order_params);

    $archives = new WP_Query($params);

    if (isset($wp_actions['_mangapress_pre_archives_get_posts'])) {
        unset($wp_actions['_mangapress_pre_archives_get_posts']);
    }

    remove_all_actions('_mangapress_pre_archives_get_posts');

    return $archives;
}

function mangapress_archive_gallery_style()
{
    $style = "
<style type=\"text/css\">
    .mangapress-archive-gallery {
        font-size: 0;
    }

    .mangapress-archive-gallery > li {
        text-align: center;
        width: 125px;
        min-height: 200px;
        font-size: 12px;
        list-style: none;
        margin: 10px;
        float: left;
    }

    .mangapress-archive-gallery > li:after {
         visibility: hidden;
         display: block;
         font-size: 0;
         content: \" \";
         clear: both;
         height: 0;
    }

    .comic-title-caption,
    .comic-post-date {
        text-align: center;
        margin: 0;
        padding: 0;
    }

    .comic-title-caption {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
</style>";

    return apply_filters('mangapress_archive_gallery_style', $style);
}