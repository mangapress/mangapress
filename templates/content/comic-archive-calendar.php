<?php
/**
 * Simple Archive Calendar template
 *
 * @package Manga_Press
 */

/**
 * WordPress DB object
 *
 * @global wpdb $wpdb
 */
global $wpdb;

$mangapress_year_results = wp_cache_get( 'mangapress_calendar_archive', 'calendar' );
if ( ! $mangapress_year_results ) {
	$mangapress_year_results = $wpdb->get_results( "SELECT DISTINCT YEAR(post_date) as year FROM {$wpdb->posts} WHERE post_type='mangapress_comic' GROUP BY post_date DESC" );
	wp_cache_set( 'mangapress_calendar_archive', $mangapress_year_results, 'calendar' );
}

foreach ( $mangapress_year_results as $year_obj ) { // @phpcs:ignore -- it's a variable for a foreach loop
	for ( $i = 1; $i <= 12; $i++ ) { // @phpcs:ignore -- same here, $i is the for-loop counter
		mangapress_get_calendar( $i, $year_obj->year, false, true );
	}
}
