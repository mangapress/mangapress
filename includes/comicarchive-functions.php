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
    $series_tax = get_terms('mangapress_series');
    $series_ids = array();
    foreach ($series_tax as $series) {
        $series_ids[] = $series->term_id;
    }
    
    do_action('_mangapress_pre_archives_get_posts');
    add_filter('posts_orderby', 'mangapress_orderby');
    $archives = new WP_Query(array(
        'post_type'       => 'mangapress_comic',
        'posts_per_page'  => -1,
        'tax_query'      => array(
            'relation' => 'AND',
            array(
                'taxonomy'   => 'mangapress_series',
                'field'      => 'id',
                'terms'      => $series_ids,
            ),
        )        
    ));
    remove_filter('posts_orderby', 'mangapress_orderby');
    
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


function mangapress_get_post_series($id)
{
    
}