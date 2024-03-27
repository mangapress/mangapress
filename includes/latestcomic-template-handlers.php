<?php
/**
 * MangaPress Latest Comic template Handlers
 *
 * @package latestcomic-template-handlers
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */

/**
 * Template handler for Latest Comic page
 *
 * @param string $default_template Default template if requested template is not found.
 *
 * @return string
 */
function mangapress_latestcomic_page_template( string $default_template ): string {

	if ( ! mangapress_is_queried_page( 'latestcomic_page' ) ) {
		return $default_template;
	}

	// maintain template hierarchy if not single.php, page.php or index.php.
	if ( ! in_array( basename( $default_template ), array( 'single.php', 'singular.php', 'page.php', 'index.php', 'template-canvas.php' ), true ) ) {
		return $default_template;
	}

	$template = locate_template( array( 'comics/latest-comic.php' ) );

	// if template can't be found, then look for query defaults...
	if ( ! $template ) {
		add_filter( 'the_content', 'mangapress_add_comic_to_latestcomic_page' );
		return $default_template;
	} else {
		return $template;
	}
}


/**
 * Add Latest Comic to page content
 *
 * @param string $content Post content being filtered.
 *
 * @return string
 * @global WP_Post $post WordPress post object.
 */
function mangapress_add_comic_to_latestcomic_page( string $content ): string {
	global $post, $wp_query;

	if ( ! mangapress_is_queried_page( 'latestcomic_page' ) ) {
		return $content;
	}

	$image_sizes = get_intermediate_image_sizes();
	$old_query   = $wp_query;
	$wp_query    = mangapress_get_latest_comic(); // @phpcs:ignore -- will be removed at a later date

	if ( ! $wp_query || ( 'no-comic-found' === $wp_query->get( 'name' ) ) ) {
		return apply_filters(
			'mangapress_the_latest_comic_content_error',
			'<p class="error">No recent comics were found.</p>'
		);
	}

	$thumbnail_size = $image_sizes['comic-page'] ?? 'large';

	$post = $wp_query->posts[0]; // @phpcs:ignore -- will be removed at a later date

	setup_postdata( $post );

	ob_start();
	require mangapress_get_content_template( 'latestcomic_page' );
	$content = ob_get_clean();

	$wp_query = $old_query; // @phpcs:ignore -- will be removed in a future version
	wp_reset_postdata();

	return apply_filters( 'mangapress_the_latest_comic_content', $content );
}
