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
function mangapress_get_all_comics_for_archive()
{
    global $wp_actions;

    // TODO Ordering options
    // TODO Sort parameters for taxonomies
    // TODO Move add/remove filter calls to separate functions
    do_action('_mangapress_pre_archives_get_posts');
    $mp_options = MangaPress_Bootstrap::get_instance()->get_options();

    $archives = new WP_Query(array(
        'post_type'       => 'mangapress_comic',
        'posts_per_page'  => -1,
        'order'         => $mp_options['basic']['archive_order'],
        'orderby'         => $mp_options['basic']['archive_orderby']
    ));

    if (isset($wp_actions['_mangapress_pre_archives_get_posts'])) {
        unset($wp_actions['_mangapress_pre_archives_get_posts']);
    }

    remove_all_actions('_mangapress_pre_archives_get_posts');

    return $archives;
}
