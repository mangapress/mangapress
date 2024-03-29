<?php
/**
 * Functions for modifying WP Query via filters
 *
 * @package Manga_Press
 * @author Jess Green <jgreen AT psy-dreamer.com>
 * @version $Id$
 */

/**
 * Set DISTINCT for comic queries
 *
 * @return string
 */
function mangapress_distinct_rows() {
	return 'DISTINCT';
}

/**
 * Modify the query limit.
 *
 * @param string   $limit SQL limit string.
 * @param WP_Query $query WP Query object.
 *
 * @return string
 */
function mangapress_post_limits( string $limit, WP_Query $query ): string {

	if ( is_admin() || $query->is_main_query() || mangapress_is_comic_archive_page() ) {
		return $limit;
	}

	return 'LIMIT 1';
}
