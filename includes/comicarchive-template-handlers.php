<?php
/**
 * MangaPress Template Handler functions
 *
 * @package MangaPress
 */

/**
 * Get Comic Archive template
 *
 * @param string $style Template style. Can be 'list', 'gallery', or 'calendar'.
 *
 * @return string
 */
function mangapress_get_comicarchive_template( string $style ): string {
	$fields  = MangaPress_Bootstrap::get_instance()->get_helper( 'options' )->options_fields();
	$options = $fields['basic']['comicarchive_page_style'];
	unset( $options['value']['no_val'] ); // remove this, we don't need it.

	if ( ! in_array( $style, array_keys( $options['value'] ), true ) ) {
		return 'comic-archive-list.php'; // return the default if the value doesn't match.
	}

	return "comic-archive-{$style}.php";
}


/**
 * Template handler for Comic Archive page
 *
 * @param string $default_template Default template if requested template is not found.
 *
 * @return string
 */
function mangapress_comicarchive_page_template( string $default_template ): string {
	if ( ! mangapress_is_queried_page( 'comicarchive_page' ) ) {
		return $default_template;
	}

	// maintain template hierarchy if not page.php or index.php.
	if ( ! in_array( basename( $default_template ), array( 'single.php', 'singular.php', 'page.php', 'index.php' ), true ) ) {
		return $default_template;
	}

	$comicarchive_page_style = MangaPress_Bootstrap::get_instance()->get_option( 'basic', 'comicarchive_page_style' );

	if ( in_array( $comicarchive_page_style, array( 'list', 'gallery', 'calendar' ), true ) ) {
		$template = locate_template(
			array(
				"comics/comic-archive-{$comicarchive_page_style}.php",
				'comics/comic-archive.php',
			)
		);
	} else {
		$template = locate_template(
			array(
				'comics/comic-archive.php',
			)
		);
	}

	// if template can't be found, then look for query defaults...
	if ( ! $template ) {
		add_filter( 'the_content', 'mangapress_create_comicarchive_page' );
		return $default_template;
	} else {
		return $template;
	}
}


/**
 * Add comic archive output to Comic Archive page content
 *
 * @access private
 * @param string $content Page content being filtered.
 * @return string
 */
function mangapress_create_comicarchive_page( $content ) {
	global $post, $wp_query;

	if ( ! mangapress_is_queried_page( 'comicarchive_page' ) ) {
		return $content;
	}
	$old_query = $wp_query;
	$wp_query  = mangapress_get_all_comics_for_archive(); // @phpcs:ignore

	if ( ! $wp_query ) {
		return '<p class="error">No comics were found.</p>';
	}

	$comicarchive_page_style = MangaPress_Bootstrap::get_instance()->get_option( 'basic', 'comicarchive_page_style' );

	ob_start();
	require mangapress_get_content_template( $comicarchive_page_style );
	$content = ob_get_clean();

	$wp_query = $old_query; // @phpcs:ignore -- will be removed at a later date

	wp_reset_query();  // @phpcs:ignore -- will be removed at a later date

	// @phpcs:ignore -- todo: need a deprecation catch for the_comicarchive_content filter.
	return apply_filters( 'mangapress_the_comicarchive_content', $content );
}
