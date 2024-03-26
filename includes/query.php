<?php
/**
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

function mangapress_post_limits( $limit, $query ) {

	if ( is_admin() || $query->is_main_query() || is_comic_archive_page() ) {
		return $limit;
	}

	return 'LIMIT 1';
}
