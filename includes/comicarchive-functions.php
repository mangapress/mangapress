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
    // TODO Ordering options
    // TODO Sort parameters for taxonomies    
    do_action('_mangapress_pre_archives_get_posts', true);
    $archives = new WP_Query(array(
        'post_type'      => 'mangapress_comic',
        'posts_per_page' => -1,
        'is_comicarchive' => true,
    ));
    
    return $archives;
}