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
    add_filter('posts_orderby', 'mangapress_orderby');
    add_filter('posts_fields', 'mangapress_archive_select_fields');
    add_filter('posts_join', 'mangapress_archive_join');
    $archives = new WP_Query(array(
        'post_type'       => 'mangapress_comic',
        'posts_per_page'  => -1,
    ));
    if (isset($wp_actions['_mangapress_pre_archives_get_posts'])) {
        unset($wp_actions['_mangapress_pre_archives_get_posts']);
    }
    remove_all_actions('_mangapress_pre_archives_get_posts');
    remove_filter('posts_orderby', 'mangapress_orderby');
    remove_filter('posts_fields', 'mangapress_archive_select_fields');
    remove_filter('posts_join', 'mangapress_archive_join');
    
    return $archives;
}


/**
 * Change orderby parameter for archive-specific loop
 * 
 * @access private
 * @global wpdb $wpdb WordPress DB object
 * @param string $order_by
 * @return string
 */
function mangapress_orderby($order_by)
{
    global $wpdb;
    
    $order_by = "{$wpdb->term_relationships}.term_taxonomy_id, {$order_by}";
    
    return $order_by;
}


/**
 * Add fields to SELECT list for archive-specific loop
 * 
 * @access private
 * @global wpdb $wpdb
 * @param string $fields
 * @param WP_Query $query
 * @return string
 */
function mangapress_archive_select_fields($fields, WP_Query $query = null)
{
    global $wpdb;

    $fields .= ", {$wpdb->terms}.term_id AS term_ID, {$wpdb->terms}.name AS term_name, {$wpdb->terms}.slug AS term_slug";

    return $fields;
}


/**
 * Add new INNER JOIN call for forcing taxonomy name and slug to end of post objects
 * 
 * @access private
 * @global wpdb $wpdb
 * @param string $join
 * @return string
 */
function mangapress_archive_join($join)
{
    global $wpdb;   

    $join .= " INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)";
    $join .= " INNER JOIN {$wpdb->terms} ON ({$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->terms}.term_id)";
 
    return $join;
}