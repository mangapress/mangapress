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
function mangapress_get_latest_comic() {
	global $wpdb;

	$post_name = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT post_name FROM ' . $wpdb->posts . '  WHERE post_type=%s AND post_status="publish" ORDER BY post_date DESC LIMIT 1',
			MangaPress_Posts::POST_TYPE
		)
	);

	if ( ! $post_name ) {
		$post_name = 'no-comic-found';
	}
	add_filter( 'post_limits', 'mangapress_post_limits', 10, 2 );
	add_filter( 'posts_distinct', 'mangapress_distinct_rows' );
	$single_comic_query = new WP_Query(
		array(
			'name'           => $post_name,
			'post_type'      => 'mangapress_comic',
			'posts_per_page' => 1,
		)
	);
	remove_filter( 'posts_distinct', 'mangapress_distinct_rows' );
	remove_filter( 'post_limits', 'mangapress_post_limits' );

	return $single_comic_query;
}


/**
 * Start a Latest Comic loop
 *
 * @since 2.9
 * @global WP_Query $wp_query
 * @return void
 */
function mangapress_start_latest_comic() {
	global $wp_query;

	do_action( 'mangapress_latest_comic_start' );

	$wp_query = mangapress_get_latest_comic(); // @phpcs:ignore

	if ( 'no-comic-found' === $wp_query->get( 'name' ) ) {
		apply_filters(
			'mangapress_the_latest_comic_content_error',
			'<p class="error">No comics was found.</p>'
		);
	}
}


/**
 * End Latest Comic loop
 *
 * @since 2.9
 * @global WP_Query $wp_query
 * @return void
 */
function mangapress_end_latest_comic() {
	global $wp_query;
	do_action( 'mangapress_latest_comic_end' );

	wp_reset_query(); // @phpcs:ignore -- this will be removed or refactored at a later date
}
