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


define( 'MP_CATEGORY_PARENTS', 1 );
define( 'MP_CATEGORY_CHILDREN', 2 );
define( 'MP_CATEGORY_ALL', 3 );

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
function mangapress_is_queried_page( $option ) {
	global $wp_query;

	$page   = MangaPress_Bootstrap::get_instance()->get_option( 'basic', $option );
	$object = $wp_query->get_queried_object();

	if ( ! isset( $object->post_name ) || $object->post_name !== $page ) {
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
function mangapress_get_content_template( $page ) {
	switch ( $page ) {
		case 'list':
			$template = 'comic-archive-list.php';
			break;

		case 'calendar':
			$template = 'comic-archive-calendar.php';
			break;

		case 'gallery':
			$template = 'comic-archive-gallery.php';
			break;

		default:
			$template = 'latest-comic.php';
	}

	$template_file_found = locate_template( array( "templates/content/{$template}" ) );
	$file                = $template_file_found
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
function mangapress_single_comic_template( $default_template ) {
	global $post;

	if ( get_post_type( $post ) !== MangaPress_Posts::POST_TYPE && ! is_single() ) {
		return $template;
	}

	$template = locate_template( array( 'comics/single-comic.php', 'single-comic.php' ) );

	if ( $template == '' ) {
		add_filter( 'post_thumbnail_html', 'mangapress_disable_post_thumbnail', 500, 2 );
		add_filter( 'the_content', 'mangapress_single_comic_content_filter' );
		return $default_template;
	} else {
		return $template;
	}
}


/**
 * Filter contents of single comic post
 *
 * @since 2.9
 *
 * @param string $content Post content
 * @return string
 */
function mangapress_single_comic_content_filter( $content ) {
	global $post;

	if ( get_post_type( $post ) !== MangaPress_Posts::POST_TYPE ) {
		return $content;
	}

	$thumbnail_size = isset( $image_sizes['comic-page'] ) ? $image_sizes['comic-page'] : 'large';

	remove_filter( 'the_content', 'mangapress_single_comic_content_filter' );
	remove_filter( 'post_thumbnail_html', 'mangapress_disable_post_thumbnail' );

	ob_start();
	require MP_ABSPATH . 'templates/single-comic.php';
	$generated_content = ob_get_contents();
	ob_end_clean();

	$content = $generated_content . $content;

	return $content;
}



/**
 * Remove post thumbnail from Comic Posts since post thumbnails are already
 * assigned when the_content filter is run
 *
 * @since 2.9
 *
 * @param string $html Generated image html
 * @param int    $post_id Post ID
 * @return string
 */
function mangapress_disable_post_thumbnail( $html, $post_id ) {
	if ( get_post_type( $post_id ) == MangaPress_Posts::POST_TYPE ) {
		return '';
	}

	return $html;
}


/**
 * Create a date-archive permalink for Comics (for monthly links)
 *
 * @param string $monthlink Existing link to be modified or replaced
 * @param string $year
 * @param string $month
 * @return string|void
 */
function mangapress_month_link( $monthlink, $year = '', $month = '' ) {
	$posts = MangaPress_Bootstrap::get_instance()->get_helper( 'posts' );
	$slug  = $posts->get_slug();

	$month_permalink = home_url( "/{$slug}/{$year}/{$month}" );
	return $month_permalink;
}


/**
 * Create a date-archive permalink for Comics
 *
 * @param string $daylink Existing link to be modified or replaced
 * @param string $year Year
 * @param string $month Month
 * @param string $day Day
 *
 * @return string
 */
function mangapress_day_link( $daylink, $year = '', $month = '', $day = '' ) {

	$posts = MangaPress_Bootstrap::get_instance()->get_helper( 'posts' );
	$slug  = $posts->get_slug();

	$relative      = "/{$slug}/{$year}/{$month}/{$day}";
	$day_permalink = home_url( $relative );

	return $day_permalink;
}


/**
 * mangapress_version()
 * echoes the current version of Manga+Press.
 * Replaces mpp_comic_version()
 *
 * @since 2.9
 * @return void
 */
function mangapress_version() {
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
function _mangapress_set_post_type_for_boundary( $query ) {
	$query->set( 'post_type', 'mangapress_comic' );
}



/**
 * Clone of WordPress function get_adjacent_post()
 * Handles looking for previous and next comics.
 *
 * @since 2.7
 *
 * @param bool   $in_same_cat Optional. Whether returned post should be in same category.
 * @param bool   $group_by_parent Optional. Whether to limit to category parent
 * @param string $taxonomy Optional. Which taxonomy to pull from.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @param bool   $previous Optional. Whether to retrieve next or previous post.
 *
 * @global WP_Post $post
 * @return string
 */
function mangapress_get_adjacent_comic( $in_same_cat = false, $group_by_parent = false, $taxonomy = 'category', $excluded_categories = '', $previous = true ) {
	global $post, $wpdb;
	if ( empty( $post ) ) {
		return null;
	}
	$current_post_date    = $post->post_date;
	$join                 = '';
	$terms_in             = '';
	$posts_in_ex_cats_sql = '';
	if ( $in_same_cat || ! empty( $excluded_categories ) ) {
		$join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id "
			. "INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
		if ( $in_same_cat && ! $group_by_parent ) {
			$cat_array = _mangapress_get_object_terms( $post->ID, $taxonomy, MP_CATEGORY_CHILDREN );
			if ( empty( $cat_array ) ) {
				$cat_array = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
			}

			$terms_in .= " AND tt.taxonomy = '{$taxonomy}' AND tt.term_id IN (" . implode( ',', $cat_array ) . ')';
		}

		if ( $in_same_cat && $group_by_parent ) {
			$cat_array = _mangapress_get_object_terms( $post->ID, $taxonomy );
			if ( empty( $cat_array ) ) {
				$cat_array = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );

				// if it's _still_ empty
				if ( empty( $cat_array ) ) {
					$cat_array = array( 0 );
				}
			}

			// use the first category...
			$ancestor_array = get_ancestors( $cat_array[0], $taxonomy );
			// if the ancestor array is empty, use the cat_array value
			if ( empty( $ancestor_array ) || count( $ancestor_array ) == 1 ) {
				$ancestor = $cat_array[0];
			} else {
				//
				// there can be only one ancestor!
				// because the default is from lowest to highest in the hierarchy
				// we flip the array to grap the top-most parent.
				$ancestor_array = array_reverse( $ancestor_array );
				$ancestor       = absint( $ancestor_array[0] );
			}

			if ( $cat_array[0] == 0 ) {
				$join = '';
			} else {
				$join .= " AND tt.taxonomy = '{$taxonomy}' AND tt.term_id = {$ancestor}";
			}
		}

		if ( $cat_array[0] == 0 ) {
			$posts_in_ex_cats_sql = '';
		} else {
			$posts_in_ex_cats_sql = "AND tt.taxonomy = '{$taxonomy}'";
		}

		if ( ! empty( $excluded_categories ) ) {
			$excluded_categories = array_map( 'intval', explode( ' and ', $excluded_categories ) );
			if ( ! empty( $cat_array ) ) {
				$excluded_categories  = array_diff( $excluded_categories, $cat_array );
				$posts_in_ex_cats_sql = '';
			}
			if ( ! empty( $excluded_categories ) ) {
				$posts_in_ex_cats_sql = " AND tt.taxonomy = '{$taxonomy}' "
					. 'AND tt.term_id NOT IN (' . implode( $excluded_categories, ',' ) . ')';
			}
		}
	}

	$adjacent  = $previous ? 'previous' : 'next';
	$op        = $previous ? '<' : '>';
	$order     = $previous ? 'DESC' : 'ASC';
	$join      = apply_filters( "mangapress_get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories );
	$where     = apply_filters( "mangapress_get_{$adjacent}_post_where", $wpdb->prepare( "WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish' $terms_in $posts_in_ex_cats_sql", $current_post_date, $post->post_type ), $in_same_cat, $excluded_categories );
	$sort      = apply_filters( "mangapress_get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1" );
	$query     = "SELECT p.* FROM $wpdb->posts AS p $join $where $sort";
	$query_key = 'mangapress_adjacent_post_' . md5( $query );
	$result    = wp_cache_get( $query_key, 'mangapress_counts' );
	if ( false !== $result ) {
		return $result;
	}
	$result = $wpdb->get_row( "SELECT p.* FROM $wpdb->posts AS p $join $where $sort" );
	if ( null === $result ) {
		$result = '';
	}
	wp_cache_set( $query_key, $result, 'counts' );
	return $result;
}


/**
 * Clone of WordPress function get_boundary_post(). Retrieves first and last
 * comic posts.
 *
 * @since 2.7
 *
 * @global WP_Post $post WordPress post object
 *
 * @param bool   $in_same_cat Optional. Whether returned post should be in same category.
 * @param bool   $group_by_parent Optional. Whether to limit to category parent
 * @param string $taxonomy Optional. Which taxonomy to pull from.
 * @param string $excluded_categories Optional. Excluded categories IDs.
 * @param bool   $start Optional. Whether to retrieve first or last post.
 *
 * @return object
 */
function mangapress_get_boundary_comic( $in_same_cat = false, $group_by_parent = false, $taxonomy = 'category', $excluded_categories = array(), $start = true ) {
	global $post;
	if ( empty( $post ) || is_attachment() ) {
		return null;
	}

	$cat_array           = array();
	$excluded_categories = array();
	if ( $in_same_cat || ! empty( $excluded_categories ) ) {
		if ( $in_same_cat && ! $group_by_parent ) {
			$cat_array = _mangapress_get_object_terms( $post->ID, $taxonomy, MP_CATEGORY_CHILDREN );
			if ( empty( $cat_array ) ) {
				$cat_array = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
			}
		}

		if ( $in_same_cat && $group_by_parent ) {
			$cat_array_children = _mangapress_get_object_terms( $post->ID, $taxonomy );
			if ( empty( $cat_array_children ) ) {
				$cat_array_children = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );

				// if it's _still_ empty
				if ( empty( $cat_array_children ) ) {
					$cat_array_children = array( 0 );
				}
			}
			// use the first category...
			$cat_array = get_ancestors( $cat_array_children[0], $taxonomy );
			// if the ancestor array is empty, use the cat_array value
			if ( empty( $cat_array ) || count( $cat_array ) == 1 ) {
				$cat_array = array( $cat_array_children[0] );
			} else {
				//
				// because the default is from lowest to highest in the hierarchy
				// we flip the array to grap the top-most parent.
				$cat_array_rev = array_reverse( $cat_array );
				$cat_array     = array( $cat_array_rev[0] );
			}
		}

		if ( ! empty( $excluded_categories ) ) {
			$excluded_categories = array_map( 'intval', explode( ',', $excluded_categories ) );
			if ( ! empty( $cat_array ) ) {
				$excluded_categories = array_diff( $excluded_categories, $cat_array );
			}
			$inverse_cats = array();
			foreach ( $excluded_categories as $excluded_category ) {
				$inverse_cats[] = $excluded_category * -1;
			}
			$excluded_categories = $inverse_cats;
		}
	}

	$cat_array = array_merge( $cat_array, $excluded_categories );
	asort( $cat_array );
	if ( $start ) {
		$cat_array = array_reverse( $cat_array );
	}

	$categories = implode( ',', $cat_array );
	if ( ! empty( $categories ) && $categories != '0' ) {
		$tax_query = array(
			array(
				'taxonomy'         => $taxonomy,
				'field'            => 'id',
				'terms'            => $categories,
				'operator'         => 'IN',
				'include_children' => ! $group_by_parent,
			),
		);
	} else {
		$tax_query = null;
	}
	$order      = $start ? 'ASC' : 'DESC';
	$post_query = array(
		'post_type'              => 'mangapress_comic',
		'posts_per_page'         => 1,
		'tax_query'              => $tax_query,
		'order'                  => $order,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
	);

	return get_posts( $post_query );
}


/**
 * Retrieve term IDs. Either child-cats or parent-cats.
 *
 * @global wpdb $wpdb
 * @param integer $object_ID Object ID
 * @param mixed   $taxonomy Taxonomy name or array of names
 * @param integer $get Whether or not to get child-cats or top-level cats
 *
 * @return array
 */
function _mangapress_get_object_terms( $object_ID, $taxonomy, $get = MP_CATEGORY_PARENTS ) {
	global $wpdb;

	if ( $get == MP_CATEGORY_PARENTS ) {
		$parents = 'AND tt.parent = 0';
	} elseif ( $get == MP_CATEGORY_CHILDREN ) {
		$parents = 'AND tt.parent != 0';
	} else {
		$parents = '';
	}

	$tax        = (array) $taxonomy;
	$taxonomies = "'" . implode( "', '", $tax ) . "'";

	$query = "SELECT t.term_id FROM {$wpdb->terms} AS t "
		. "INNER JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_id = t.term_id "
		. "INNER JOIN {$wpdb->term_relationships} AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id "
		. "WHERE tt.taxonomy IN ({$taxonomies}) "
		. "AND tr.object_id IN ({$object_ID}) "
		. "{$parents} ORDER BY t.term_id ASC";

	return $wpdb->get_col( $query );
}
