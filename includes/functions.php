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

require_once MP_ABSPATH . 'includes/latestcomic-functions.php';
require_once MP_ABSPATH . 'includes/latestcomic-template-handlers.php';



/**
 * mpp_comic_archivepage()
 *
 * @global object $post WordPress Post
 *
 * @since 2.7
 * @param string $template
 * @return string|void
 */
function mpp_comic_archivepage($template)
{

    if (!mpp_is_queried_page('comicarchive_page')) {
        return $template;
    }
     
    $template = locate_template(array('comics/comic-archive.php'));
    if (!$template) {
        add_filter('the_content', 'mpp_filter_comic_archivepage');
        return get_page_template();
    }
    
    return $template;
}


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
function mpp_is_queried_page($option)
{
    global $wp_query;

    $page = MangaPress_Bootstrap::get_instance()->get_option('basic', $option);
    $object = $wp_query->get_queried_object();
    if (!isset($object->post_name) 
            || !($object->post_name == $page)) {
        return false;
    }
    
    return true;
}


/**
 * Turns taxonomies associated with comics into comic archives.
 *
 * @global WP_Query $wp_query
 * @param string $template
 *
 * @return void|string
 */
function mpp_series_template($template)
{
    global $wp_query;

    // is the query is not a taxonomy query, then return
    $object = $wp_query->get_queried_object();

    if (!isset($object->taxonomy) || !($object->taxonomy == 'mangapress_series')){
        return $template;
    }
    
    $template = locate_template(array('comics/archives.php'), true);
    if ($template == '') {
        return get_archive_template();
    }
    
    return $template;
}


/**
 * filter_comic_archivepage()
 *
 *
 * @global WP_Post $post WordPress Post
 *
 * @since 2.6
 * @param string $content Page content (from the_content())
 * @return string
 */
function mpp_filter_comic_archivepage($content)
{
    global $post;

    $mp_options = MangaPress_Bootstrap::get_instance()->get_options();

    if (!($post->post_name == $mp_options['basic']['comicarchive_page'])) {
        return $content;
    } else {

        $file = locate_template(array('templates/content/comic-archive.php'))
                    ? locate_template(array('templates/content/comic-archive.php'))
                    : MP_ABSPATH . 'templates/content/comic-archive.php';

        ob_start();
        require $file;
        $content = ob_get_contents();
        ob_end_clean();

        return apply_filters('the_archive_content', $content);

    }

}


/**
 * mpp_comic_single_page()
 * Uses a template to create comic navigation.
 *
 * @since 2.5
 *
 * @global object $post Wordpress post object.
 * @global int $id Post ID. Not used.
 * @global int $cat Category ID. Not used.
 * @global array $mp_options Array containing Manga+Press options.
 *
 * @return string|void
 */
function mpp_comic_single_page($template)
{
    global $wp_query;

    $object = $wp_query->get_queried_object();

    if (!isset($object->post_type) 
            || !($object->post_type == 'mangapress_comic' && is_single())) {
        return $template;
    }

    $single_comic_templates = apply_filters(
        'template_include_single_comic',
        array(
            'comics/single-comic.php',
            'single-comic.php',
        )
    );
    
    $template = locate_template($single_comic_templates, true);
    
    if ($template == '') {
        load_template(MP_ABSPATH . 'templates/single-comic.php');
    } else {
        load_template($template);
    }        
}


/**
 * mpp_comic_insert_navigation()
 * Inserts navigation on single comic pages when Insert Navigation is enabled.
 *
 * @since 2.5
 *
 * @global object $post Wordpress post object.
 *
 * @return void
 */
function mpp_comic_insert_navigation($content)
{
    global $post;

    if (!($post->post_type == 'mangapress_comic' && is_single())) {
        return $content;
    } else {
        $navigation = mangapress_comic_navigation(null, null, false);

        $content = $navigation . $content;

        return apply_filters('the_comic_content', $content);
    }

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
    _deprecated_function(__FUNCTION__, '2.9', 'get_adjacent_post()');
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
    _deprecated_function(__FUNCTION__, '2.9', 'get_boundary_post()');
}


/**
 * mpp_comic_version()
 * echoes the current version of Manga+Press.
 * @since 2.0
 * @return void
 */
function mpp_comic_version()
{
    echo MP_VERSION;
}


/**
 * Retrieve term IDs. Either child-cats or parent-cats.
 *
 * @deprecated since 2.9
 * @return void
 */
function _mangapress_get_object_terms($object_ID, $taxonomy, $get = '')
{
    _deprecated_function(__FUNCTION__, '2.9');
}


/**
 * Set the post-type for get_boundary_post()
 * Workaround for issue #27094 {@link https://core.trac.wordpress.org/ticket/27094}
 * 
 * @param WP_Query $query
 * @return void
 */
function _mangapress_set_post_type_for_boundary($query)
{
    $query->set('post_type', 'mangapress_comic');
}