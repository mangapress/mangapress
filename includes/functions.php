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

/*------------------------------------------------------------------------------
 * Manga+Press Hook Functions
 */


/**
 * Handles display for the latest comic page.
 *
 * @global WP_Post $post WordPress Post
 *
 * @since 2.7
 * @param string $content. Post content
 * @return string
 */
function mpp_filter_latest_comic($content)
{
    global $post, $wp_query;    

    $mp_options = MangaPress_Bootstrap::get_instance()->get_options();

    if (!($post->post_name == $mp_options['basic']['latestcomic_page'])) {
        return $content;
    }

    $image_sizes = get_intermediate_image_sizes();
    $wp_query = mpp_get_latest_comic();

    if (!$wp_query){
        return apply_filters(
            'the_latest_comic_content_error',
            '<p class="error">No Latest Comic was found.</p>'
        );
    }

    $thumbnail_size = isset($image_sizes['comic-page']) 
                        ? $image_sizes['comic-page'] : 'large';
    
    $post = $wp_query->posts[0];

    setup_postdata($post);
    
    $template_file_found = locate_template(array('templates/content/latest-comic.php'));
    $file = $template_file_found
                ? $template_file_found : MP_ABSPATH . 'templates/content/latest-comic.php';

    ob_start();
    require $file;
    $content = ob_get_clean();    
    
    wp_reset_query();
    
    return apply_filters('the_latest_comic_content', $content);
}


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


/**
 * mpp_latest_comic_page()
 * Set Latest Comic page template
 *
 * @global WP_Query $wp_query
 *
 * @since 2.7
 * @param string $template
 * @return string|void
 */
function mpp_latest_comic_page($template)
{
    global $wp_query;

    $mp_options = MangaPress_Bootstrap::get_instance()->get_options();

    $object     = $wp_query->get_queried_object();
    
    if (!isset($object->post_name) 
            || !($object->post_name == $mp_options['basic']['latestcomic_page'])) {
        return $template;
    }

    $template = locate_template(array('comics/latest-comic.php'));

    // if template can't be found, then look for query defaults...
    if (!$template) {
        add_filter('the_content', 'mpp_filter_latest_comic');
        return get_page_template();
    } else {
        return $template;
    }

}


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
    global $wp_query;

    $mp_options = MangaPress_Bootstrap::get_instance()->get_options();

    $object = $wp_query->get_queried_object();

    if (!isset($object->post_name) 
            || !($object->post_name == $mp_options['basic']['comicarchive_page'])) {
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

add_filter('template_include', 'mpp_comic_archivepage');
add_filter('template_include', 'mpp_latest_comic_page');
