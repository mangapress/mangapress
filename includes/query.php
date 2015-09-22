<?php
/**
 * @package Manga_Press
 * @author Jess Green <jgreen AT psy-dreamer.com>
 * @version $Id$
 */



/**
 * Set DISTINCT for comic queries
 * @return string
 */
function mangapress_distinct_rows()
{
    return "DISTINCT";
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


function mangapress_post_limits($limit, $query){

    if (is_admin() || $query->is_main_query() || is_comic_archive_page())
        return $limit;

    return "LIMIT 1";
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
function mangapress_select_fields($fields, WP_Query $query = null)
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
function mangapress_join($join)
{
    global $wpdb;

    $join .= " INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)";
    $join .= " INNER JOIN {$wpdb->terms} ON ({$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->terms}.term_id)";

    return $join;
}