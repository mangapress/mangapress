<?php
/**
 * Manga+Press plugin Functions
 * This is where the actual work gets done...
 *
 * @package Manga_Press
 * @subpackage Core_Functions
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */

require_once MP_ABSPATH . 'includes/query.php';
require_once MP_ABSPATH . 'includes/latestcomic-functions.php';
require_once MP_ABSPATH . 'includes/comicarchive-functions.php';
require_once MP_ABSPATH . 'includes/latestcomic-template-handlers.php';
require_once MP_ABSPATH . 'includes/comicarchive-template-handlers.php';


define('MP_CATEGORY_PARENTS', 1);
define('MP_CATEGORY_CHILDREN', 2);
define('MP_CATEGORY_ALL', 3);

/**
 * Checks queried object against settings to see if query is for either
 * latest comic or comic archive.
 * 
 * @since 2.9
 * 
 * @global WP_Query $wp_query
 * @param string $option Name of option to retrieve. Should be latestcomic_page or comicarchive_page
 * @return boolean
 */
function mangapress_is_queried_page($option)
{
    global $wp_query; 

    $page = MangaPress_Bootstrap::get_instance()->get_option('basic', $option);
    $object = $wp_query->get_queried_object();

    if (!isset($object->post_name) || $object->post_name !== $page) {
        return false;
    }
    
    return true;
}


/**
 * Return plugin-default template location for latest comic or archive
 * 
 * @since 2.9
 * @param string $page Which page to get default template for
 * @return string
 */
function mangapress_get_content_template($page)
{
    $template = 'latest-comic.php';
    if ($page !== 'latestcomic_page') {
        $template = 'comic-archive.php';
    } 
    
    $template_file_found = locate_template(array("templates/content/{$template}"));
    $file = $template_file_found 
            ? $template_file_found : MP_ABSPATH . "templates/content/{$template}";
        
    return $file;    
}


/**
 * Add additional templates to the mangapress_comic template stack
 * 
 * @since 2.9
 * 
 * @global WP_Post $post WordPress post object
 * @param string $template Template filename
 * @return string
 */
function mangapress_single_comic_template($template)
{
    global $post;

    $templates = array('comics/single-comic.php', 'single-comic.php',);
    
    if (get_post_type($post) !== MangaPress_Posts::POST_TYPE && !is_single()) {
        return $template;
    }
//var_dump($template);
    /**
     * mangapress_comic_single
     * replaces: template_include_single_comic
     * 
     * @since 2.9
     * @return string
     */        
    $template = locate_template(apply_filters('mangapress_comic_single', $templates), true);
    var_dump($template);
    if ($template == '') {
        return MP_ABSPATH . 'templates/single-comic.php';
    } else {
        return $template;
    }        
}


/**
 * mpp_comic_insert_navigation()
 * Inserts navigation on single comic pages when Insert Navigation is enabled.
 *
 * @since 2.8
 *
 * @global object $post Wordpress post object.
 *
 * @return string
 */
function mangapress_comic_insert_navigation($content)
{
    global $post;

    if (!(get_post_type($post) == 'mangapress_comic' && is_single())) {
        return $content;
    } else {
        $navigation = mangapress_comic_navigation(null, null, false);

        $content = $navigation . $content;

        return apply_filters('the_comic_content', $content);
    }

}


/**
 * mangapress_version()
 * echoes the current version of Manga+Press.
 * Replaces mpp_comic_version()
 * 
 * @since 2.9
 * @return void
 */
function mangapress_version()
{
    echo MP_VERSION;
}


/**
 * Set the post-type for get_boundary_post()
 * Workaround for issue #27094 {@link https://core.trac.wordpress.org/ticket/27094}
 * 
 * @access private
 * @param WP_Query $query
 * @return void
 */
function _mangapress_set_post_type_for_boundary($query)
{
    $query->set('post_type', 'mangapress_comic');
}


/**
 * Clone of WordPress function get_adjacent_post()
 * Handles looking for previous and next comics.
 *
 * @since 2.7
 *
 * @param bool $in_same_cat Optional. Whether returned post should be in same category.
 * @param bool $group_by_parent Optional. Whether to limit to category parent
 * @param string $taxonomy Optional. Which taxonomy to pull from.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @param bool $previous Optional. Whether to retrieve next or previous post.
 *
 * @global WP_Post $post
 * @return string
 */
function mangapress_get_adjacent_comic($in_same_cat = false, $group_by_parent = false, $taxonomy = 'category', $excluded_categories = '', $previous = true)
{
    global $post;

    $cat_array = array();
    if ($group_by_parent) {
        $cat_array = _mangapress_get_object_terms($post->ID, $taxonomy, MP_CATEGORY_CHILDREN);
    } else {
        $cat_array = _mangapress_get_object_terms($post->ID, $taxonomy, MP_CATEGORY_PARENTS);
    }

    return get_adjacent_post($in_same_cat, $cat_array, $previous, $taxonomy);
}


/**
 * Clone of WordPress function get_boundary_post(). Retrieves first and last
 * comic posts. 
 *
 * @since 2.7
 *
 * @global WP_Post $post WordPress post object
 *
 * @param bool $in_same_cat Optional. Whether returned post should be in same category.
 * @param bool $group_by_parent Optional. Whether to limit to category parent
 * @param string $taxonomy Optional. Which taxonomy to pull from.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @param bool $start Optional. Whether to retrieve first or last post.
 *
 * @return object
 */
function mangapress_get_boundary_comic($in_same_cat = false, $group_by_parent = false, $taxonomy = 'category', $excluded_categories = '', $start = true)
{
    global $post;

    $cat_array = array();
    if ($group_by_parent) {
        $cat_array = _mangapress_get_object_terms($post->ID, $taxonomy, MP_CATEGORY_CHILDREN);
    } else {
        $cat_array = _mangapress_get_object_terms($post->ID, $taxonomy, MP_CATEGORY_PARENTS);
    }

    return get_boundary_post($in_same_cat, $cat_array, $start, $taxonomy);
}


/**
 * Retrieve term IDs. Either child-cats or parent-cats.
 *
 * @global wpdb $wpdb
 * @param integer $object_ID Object ID
 * @param mixed $taxonomy Taxonomy name or array of names
 * @param boolean $exclude_with_parents Whether or not to get child-cats or top-level cats
 *
 * @return array
 */
function _mangapress_get_object_terms($object_ID, $taxonomy, $get = MP_CATEGORY_PARENTS)
{
    global $wpdb;

    if ($get == MP_CATEGORY_PARENTS) {
        $parents = "AND tt.parent = 0";
    } else if ($get == MP_CATEGORY_CHILDREN) {
        $parents = "AND tt.parent != 0";
    } else {
        $parents = "";
    }

    $tax = (array) $taxonomy;
    $taxonomies = "'" . implode("', '", $tax) . "'";

    $query = "SELECT t.term_id FROM {$wpdb->terms} AS t "
             . "INNER JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_id = t.term_id "
             . "INNER JOIN {$wpdb->term_relationships} AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id "
             . "WHERE tt.taxonomy IN ({$taxonomies}) "
             . "AND tr.object_id IN ({$object_ID}) "
             . "{$parents} ORDER BY t.term_id ASC";

    return $wpdb->get_col($query);

}


/**
 * mpp_get_adjacent_comic()
 * Deprecated function. Do not use.
 *
 * @since 2.7
 * @deprecated since 2.9. Use get_adjacent_post();
 *
 * @return void
 */
function mpp_get_adjacent_comic($in_same_cat = false, $group_by_parent = false, $taxonomy = 'category', $excluded_categories = '', $previous = true)
{
    _deprecated_function(__FUNCTION__, '2.9', 'mangapress_get_adjacent_comic()');
}


/**
 * mpp_get_boundary_comic()
 * Deprecated function. Do not use.
 * 
 * @since 2.7
 * @deprecated since 2.9. Use get_boundary_comic()
 * @return void
 */
function mpp_get_boundary_comic($in_same_cat = false, $group_by_parent = false, $taxonomy = 'category', $excluded_categories = '', $start = true)
{
    _deprecated_function(__FUNCTION__, '2.9', 'mangapress_get_boundary_comic()');
}


/**
 * mpp_comic_single_page()
 * Uses a template to create comic navigation.
 * 
 * @deprecated since 2.9
 * @return void
 */
function mpp_comic_single_page($template)
{
    _deprecated_function(__FUNCTION__, '2.9');
}


/**
 * mpp_comic_insert_navigation()
 * Insert comic navigation
 * 
 * @global WP_Post $post
 * @deprecated since 2.8
 * @return void
 */
function mpp_comic_insert_navigation($content)
{
    global $post;
    
    _deprecated_function(__FUNCTION__, '2.8', 'mangapress_comic_insert_navigation()');
    mangapress_comic_insert_navigation($content);
}