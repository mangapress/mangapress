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

define('MP_CATEGORY_PARENTS', 1);
define('MP_CATEGORY_CHILDREN', 2);
define('MP_CATEGORY_ALL', 3);

/*------------------------------------------------------------------------------
 * Manga+Press Hook Functions
 */

/**
 * Handles display for the latest comic page.
 *
 * @global WP_Post $post WordPress Post
 *
 * @since 2.7
 * @param string $template
 * @return string
 */
function mpp_filter_latest_comic($content)
{
    global $post;

    $image_sizes = get_intermediate_image_sizes();

    $mp_options = MangaPress_Bootstrap::get_instance()->get_options();

    if (!($post->post_name == $mp_options['basic']['latestcomic_page'])) {
        return $content;
    } else {

        $single_comic_query = mpp_get_latest_comic();
        if ($single_comic_query instanceof WP_Error || $single_comic_query->get('name') == 'no-comic-found'){
            return apply_filters(
                'the_latest_comic_content_error',
                '<p class="error">No comics was found.</p>'
            );
        }

        $thumbnail_size = 'comic-page';
        if (!isset($image_sizes['comic-page'])) {
            $thumbnail_size = 'large';
        }

        $post = $single_comic_query->posts[0];

        setup_postdata($post);

        $file = locate_template(array('templates/content/latest-comic.php'))
                    ? locate_template(array('templates/content/latest-comic.php'))
                    : MP_ABSPATH . 'templates/content/latest-comic.php';

        ob_start();
        require $file;
        $content = ob_get_contents();
        ob_end_clean();

        return apply_filters('the_latest_comic_content', $content);

    }
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
        $post_name = 'no-comic-found';
    }

    $single_comic_query = new WP_Query(array(
        'name'      => $post_name,
        'post_type' => 'mangapress_comic',
    ));

    return $single_comic_query;
}


/**
 * Overrides mpp_filter_latest_comic() with a template.
 *
 * @global WP_Query $wp_query
 *
 * @since 2.7
 * @param string $default_template Default template passed via template_include
 * @return string|void
 */
function mpp_latest_comic_page($default_template)
{
    global $wp_query;

    $mp_options = MangaPress_Bootstrap::get_instance()->get_options();
    $object     = $wp_query->get_queried_object();
    
    if (!isset($object->post_name) 
            || !($object->post_name == $mp_options['basic']['latestcomic_page'])) {
        return $default_template;
    }

    $latest_template = apply_filters(
        'template_include_latest_comic',
        array('comics/latest-comic.php',)
    );
    $template = locate_template($latest_template);

    // if template can't be found, then look for query defaults...
    if (!$template) {
        return $default_template;
    } else {
        return $template;
    }

}


/**
 * Turns taxonomies associated with comics into comic archives.
 *
 * @global WP_Query $wp_query
 * @param string $default_template Default template passed via template_include
 *
 * @return void|string
 */
function mpp_series_template($default_template)
{
    global $wp_query;

    // is the query is not a taxonomy query, then return
    $object = $wp_query->get_queried_object();

    if (!isset($object->taxonomy) || !($object->taxonomy == 'mangapress_series')){
        return $default_template;
    }
    
    $template = locate_template(array('comics/archives.php'), true);
    if ($template == '') {
        return $default_template;
    }
    
    return $template;
}


/**
 * comic_archivepage()
 *
 * @global object $post WordPress Post
 *
 * @since 2.7
 * @param string $template Default template passed via template_include
 * @return string|void
 */
function mpp_comic_archivepage($default_template)
{
    global $wp_query;

    $mp_options = MangaPress_Bootstrap::get_instance()->get_options();

    $object = $wp_query->get_queried_object();

    if (!isset($object->post_name) 
            || !($object->post_name == $mp_options['basic']['comicarchive_page'])) {
        return $default_template;
    }
    
    $archive_templates = apply_filters(
        'template_include_comic_archive',
        array('comics/comic-archive.php')
    );

    // if template can't be found, then look for query defaults...
    $template = locate_template($archive_templates, true);
    if ($template == '') {
        return $default_template;
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

    if (!($post->post_type == 'mangapress_comic' && is_single())){
        return $content;
    } else {
        $navigation = mangapress_comic_navigation(null, null, false);

        $content = $navigation . $content;

        return apply_filters('the_comic_content', $content);
    }

}

/**
 * Clone of WordPress function get_adjacent_post()
 * Handles looking for previos and next comics. Needed because get_adjacent_post()
 * will only handle category, and not other taxonomies. Addresses issue with
 * get_adjacent_post() from {@link http://core.trac.wordpress.org/ticket/17807 WordPress Trac #17807}
 * May be deprecated once WordPress Trac #17807 is resolved, possibly in WP 3.5
 *
 * @since 2.7
 *
 * @param bool $in_same_cat Optional. Whether returned post should be in same category.
 * @param string $taxonomy Optional. Which taxonomy to pull from.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @param string $previous Optional. Whether to retrieve next or previous post.
 *
 * @global WP_Post $post
 * @global wpdb $wpdb
 *
 * @return string
 */
function mpp_get_adjacent_comic($in_same_cat = false, $group_by_parent = false, $taxonomy = 'category', $excluded_categories = '', $previous = true)
{
    global $post, $wpdb;

    if ( empty( $post ) )
            return null;

    $current_post_date = $post->post_date;

    $join = '';
    $posts_in_ex_cats_sql = '';
    if ($in_same_cat || !empty($excluded_categories)) {
        $join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id "
              . "INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";

        if ( $in_same_cat && !$group_by_parent) {
            $cat_array = _mangapress_get_object_terms($post->ID, $taxonomy, MP_CATEGORY_CHILDREN);

            // if $cat_array returns empty
            $terms_in = "";
            if (!empty($cat_array)) {
                $terms_in = "AND tt.term_id IN (" . implode(',', $cat_array) . ")";
            } else {
                $cat_array = _mangapress_get_object_terms($post->ID, $taxonomy, MP_CATEGORY_ALL);
            }

            $join .= " AND tt.taxonomy = '{$taxonomy}' {$terms_in}";
        }

        if ( $in_same_cat && $group_by_parent) {
            $cat_array = _mangapress_get_object_terms($post->ID, $taxonomy);

            // use the first category...
            $ancestor_array = get_ancestors($cat_array[0], $taxonomy);

            // if the ancestor array is empty, use the cat_array value
            if (empty($ancestor_array)) {
                $ancestor = $cat_array[0];
            } else {
                //
                // there can be only one ancestor!
                // because the default is from lowest to highest in the hierarchy
                // we flip the array to grap the top-most parent.
                $ancestor_array = array_reverse($ancestor_array);
                $ancestor = absint($ancestor_array[0]);
            }

            $join .= " AND tt.taxonomy = '{$taxonomy}' AND tt.term_id = {$ancestor}";
        }

        $posts_in_ex_cats_sql = "AND tt.taxonomy = '{$taxonomy}'";
        if ( !empty($excluded_categories) ) {
            $excluded_categories = array_map('intval', explode(' and ', $excluded_categories));
            if ( !empty($cat_array) ) {
                    $excluded_categories = array_diff($excluded_categories, $cat_array);
                    $posts_in_ex_cats_sql = '';
            }

            if ( !empty($excluded_categories) ) {
                    $posts_in_ex_cats_sql = " AND tt.taxonomy = '{$taxonomy}' "
                                           . "AND tt.term_id NOT IN (" . implode($excluded_categories, ',') . ')';
            }
        }
    }

    $adjacent = $previous ? 'previous' : 'next';
    $op       = $previous ? '<' : '>';
    $order    = $previous ? 'DESC' : 'ASC';

    $join  = apply_filters( "get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories );
    $where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish' $posts_in_ex_cats_sql", $current_post_date, $post->post_type), $in_same_cat, $excluded_categories );
    $sort  = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1" );

    $query = "SELECT p.* FROM $wpdb->posts AS p $join $where $sort";

    $query_key = 'adjacent_post_' . md5($query);
    $result = wp_cache_get($query_key, 'counts');
    if ( false !== $result )
            return $result;

    $result = $wpdb->get_row("SELECT p.* FROM $wpdb->posts AS p $join $where $sort");
    if ( null === $result )
            $result = '';

    wp_cache_set($query_key, $result, 'counts');

    return $result;
}

/**
 * Clone of WordPress function get_boundary_post(). Retrieves first and last
 * comic posts. Needed because get_boundary_post() will only handle category,
 * and not other taxonomies. Addresses issues with get_boundary_post() in
 * {@link http://core.trac.wordpress.org/ticket/17807 WordPress Trac #17807}
 * May be deprecated once WordPress Trac #17807 is resolved, possibly in WP 3.5
 *
 * @since 2.7
 *
 * @global WP_Post $post WordPress post object
 *
 * @param bool $in_same_cat Optional. Whether returned post should be in same category.
 * @param string $taxonomy Optional. Which taxonomy to pull from.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @param bool $start Optional. Whether to retrieve first or last post.
 *
 * @return object
 */
function mpp_get_boundary_comic($in_same_cat = false, $group_by_parent = false, $taxonomy = 'category', $excluded_categories = '', $start = true)
{
    global $post;

    if ( empty($post) || is_attachment() )
        return null;

    $cat_array = array();
    $excluded_categories = array();
    if ($in_same_cat || !empty($excluded_categories)) {
        if ($in_same_cat && !$group_by_parent) {
            $cat_array = _mangapress_get_object_terms($post->ID, $taxonomy, MP_CATEGORY_CHILDREN);
        }

        if ( $in_same_cat && $group_by_parent) {
            $cat_array_children = _mangapress_get_object_terms($post->ID, $taxonomy);

            // use the first category...
            $cat_array = get_ancestors($cat_array_children[0], $taxonomy);
            // if the ancestor array is empty, use the cat_array value
            if (empty($cat_array)) {
                $cat_array = array($cat_array_children[0]);
            } else {
                //
                // because the default is from lowest to highest in the hierarchy
                // we flip the array to grap the top-most parent.
                $cat_array_rev = array_reverse($cat_array);
                $cat_array = array($cat_array_rev[0]);
            }
        }

        if ( !empty($excluded_categories) ) {
            $excluded_categories = array_map('intval', explode(',', $excluded_categories));

            if ( !empty($cat_array) )
                $excluded_categories = array_diff($excluded_categories, $cat_array);

            $inverse_cats = array();
            foreach ( $excluded_categories as $excluded_category)
                $inverse_cats[] = $excluded_category * -1;
            $excluded_categories = $inverse_cats;
        }
    }

    $cat_array = array_merge($cat_array, $excluded_categories);
    asort($cat_array);

    if ($start) {
        $cat_array = array_reverse($cat_array);
    }

    $categories = implode(',',  $cat_array);
    if (!empty($categories)) {
        $tax_query = array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'id',
                'terms'    => $categories,
                'operator' => 'IN'
            )
        );
    } else {
        $tax_query = null;
    }

    $order = $start ? 'ASC' : 'DESC';
    $post_query = array(
        'post_type'              => 'mangapress_comic',
        'numberposts'            => 1,
        'tax_query'              => $tax_query,
        'order'                  => $order,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
    );

    return get_posts($post_query);
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

    $query = "SELECT t.term_id FROM $wpdb->terms AS t "
             . "INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id "
             . "INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id "
             . "WHERE tt.taxonomy IN ($taxonomies) "
             . "AND tr.object_id IN ({$object_ID}) "
             . "{$parents} ORDER BY t.term_id ASC";

    return $wpdb->get_col($query);

}
