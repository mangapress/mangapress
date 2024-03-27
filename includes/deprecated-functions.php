<?php
/**
 * Deprecated functions
 * This file has the functions that will be deprecated in a future version
 *
 * @package MangaPress
 */

// @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound,Squiz.Commenting.FunctionComment.MissingParamTag,Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
if ( ! function_exists( 'is_comic' ) ) {
	/**
	 * Checks if post is a comic post.
	 *
	 * @see mangapress_is_comic()
	 * @deprecated 3.0.3
	 */
	function is_comic( $post = null ): bool {
		_deprecated_function( __FUNCTION__, 'This function is deprecated and will be removed in a future version. Please use mangapress_is_comic() instead.' );
		return mangapress_is_comic( $post );
	}
}

if ( ! function_exists( 'is_comic_page' ) ) {
	/**
	 * Checks if the page is a comic page (List or Archive)
	 *
	 * @see mangapress_is_comic_page()
	 * @deprecated 3.0.3
	 */
	function is_comic_page(): bool {
		_deprecated_function( __FUNCTION__, 'This function is deprecated and will be removed in a future version. Please use mangapress_is_comic_page() instead.' );
		return mangapress_is_comic_page();
	}
}

if ( ! function_exists( 'is_comic_archive_page' ) ) {
	/**
	 * Checks if the page is a comic archive page
	 *
	 * @see mangapress_is_comic_archive_page()
	 * @deprecated 3.0.3
	 */
	function is_comic_archive_page(): bool {
		_deprecated_function( __FUNCTION__, 'This function is deprecated and will be removed in a future version. Please use mangapress_is_comic_archive_page() instead.' );

		return mangapress_is_comic_archive_page();
	}
}


/**
 * Retrieve the previous post in The Loop. We have our reasons
 *
 * @deprecated 3.0.3
 * @global WP_Query $wp_query
 * @return WP_Post|false
 */
function mangapress_get_previous_post_in_loop() {
	_deprecated_function( __FUNCTION__, 'This function is deprecated and should not be used as it will be removed in a future version.' );
	global $wp_query;
	if ( -1 === $wp_query->current_post || 0 === $wp_query->current_post ) {
		return false;
	}

	return $wp_query->posts[ $wp_query->current_post - 1 ];
}


/**
 * Get the next post in the loop. Might come in handy.
 *
 * @deprecated 3.0.3
 *
 * @global WP_Query $wp_query
 * @return WP_Post|false
 */
function mangapress_get_next_post_in_loop() {
	_deprecated_function( __FUNCTION__, 'This function is deprecated and should not be used as it will be removed in a future version.' );
	global $wp_query;

	_deprecated_function( __FUNCTION__, 'This function will be removed in a future version of Manga+Press' );

	if ( ( $wp_query->found_posts - 1 ) === $wp_query->current_post ) {
		return false;
	}

	return $wp_query->posts[ $wp_query->current_post + 1 ];
}


/**
 * Get comic term ID.
 *
 * @deprecated 3.0.3
 *
 * @param WP_Post|int $post WordPress post object or post ID.
 * @return false|int
 */
function mangapress_get_comic_term_ID( $post = 0 ) {

	_deprecated_function( __FUNCTION__, 'This function will be removed in a future version of Manga+Press' );

	if ( false === $post ) {
		return false;
	}

	$post = get_post( $post );
	if ( ! isset( $post->term_ID ) ) { // @phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		return false;
	}

	return $post->term_ID; // @phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
}



/**
 * Get comic slug
 *
 * @param WP_Post|int $post WordPress post object or post ID.
 * @return false|string
 */
function mangapress_get_comic_term_title( $post = 0 ) {
	_deprecated_function( __FUNCTION__, 'This function will be removed in a future version of Manga+Press' );

	$post = get_post( $post );
	if ( ! isset( $post->term_name ) ) {
		return false;
	}

	return $post->term_name;
}

/**
 * Clone of WordPress function @see get_adjacent_post()
 * Handles looking for previous and next comics.
 *
 * @deprecated 3.0.3
 * @return string
 */
function mangapress_get_adjacent_comic( $in_same_cat = false, $group_by_parent = false, $taxonomy = 'category', $excluded_categories = '', $previous = true ) {
	_deprecated_function( __FUNCTION__, 'This function is deprecated. Please use get_adjacent_post instead.' );
	return get_adjacent_post( $in_same_cat, '', $previous, $taxonomy );
}

/**
 * Clone of WordPress function @see get_boundary_post()
 * Handles looking for previous and next comics.
 *
 * @deprecated 3.0.3
 * @return array
 */
function mangapress_get_boundary_comic( $in_same_cat = false, $group_by_parent = false, $taxonomy = 'category', $excluded_categories = array(), $start = true ) {
	_deprecated_function( __FUNCTION__, 'This function is deprecated. Please use get_boundary_post instead.' );
	return get_boundary_post( $in_same_cat, '', $start, $taxonomy );
}

/**
 * Retrieve term IDs. Either child-cats or parent-cats. Clone of @see wp_get_object_terms()
 *
 * @deprecated 3.0.3
 * @return array
 */
function mangapress_get_object_terms( int $object_id, $taxonomy, int $get = MP_CATEGORY_PARENTS ): array {
	_deprecated_function( __FUNCTION__, 'This function is deprecated. Please use wp_get_object_terms instead.' );
	return wp_get_object_terms( $object_id, $taxonomy );
}
