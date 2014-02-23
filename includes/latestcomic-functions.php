<?php
/**
 * Latest Comic functions
 * All functions that handle retrieval and display of the most recent
 * comic page
 * 
 * @package latestcomic-functions
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */

/**
 * Retrieves the most recent comic
 *
 * @since 2.7.2
 * @return \WP_Query
 */
function mpp_get_latest_comic()
{
    global $wpdb;

    $sql = "SELECT post_name FROM {$wpdb->posts} "
         . "WHERE post_type=\"mangapress_comic\" "
         . "AND post_status=\"publish\" "
         . "ORDER BY post_date DESC LIMIT 1";

    $post_name = $wpdb->get_var($sql);

    if (!$post_name) {
        return false;
    }

    $single_comic_query = new WP_Query(array(
        'name'      => $post_name,
        'post_type' => 'mangapress_comic',
    ));

    return $single_comic_query;
}


/**
 * Start a Latest Comic loop
 * 
 * @global WP_Query $wp_query
 * @return void
 */
function mpp_start_latest_comic()
{
    global $wp_query;
    
    do_action('latest_comic_start');
    
    $wp_query = mpp_get_latest_comic();
}


/**
 * End Latest Comic loop
 * 
 * @global WP_Query $wp_query
 * @return void
 */
function mpp_end_latest_comic()
{
    global $wp_query;
    do_action('latest_comic_end');
    
    wp_reset_query();
}

