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

/**
 * Includes of template files.
 */
require_once MP_ABSPATH . 'includes/query.php';
require_once MP_ABSPATH . 'includes/latestcomic-functions.php';
require_once MP_ABSPATH . 'includes/comicarchive-functions.php';
require_once MP_ABSPATH . 'includes/latestcomic-template-handlers.php';
require_once MP_ABSPATH . 'includes/comicarchive-template-handlers.php';


const MP_CATEGORY_PARENTS  = 1;
const MP_CATEGORY_CHILDREN = 2;
const MP_CATEGORY_ALL      = 3;

/**
 * Checks queried object against settings to see if query is for either
 * latest comic or comic archive.
 *
 * @param string $option Name of option to retrieve. Should be latestcomic_page or comicarchive_page.
 *
 * @return boolean
 * @since 2.9
 *
 * @global WP_Query $wp_query
 */
function mangapress_is_queried_page( string $option ): bool {
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
 * @param string $page Which page to get default template for.
 *
 * @return string
 * @since 2.9
 */
function mangapress_get_content_template( string $page ): string {
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
 * @param string $default_template Template filename.
 *
 * @return string
 * @since 2.9
 *
 * @global WP_Post $post WordPress post object.
 */
function mangapress_single_comic_template( string $default_template ): string {
	global $post;

	if ( get_post_type( $post ) !== MangaPress_Posts::POST_TYPE && ! is_single() ) {
		return $default_template;
	}

	$default_template = locate_template( array( 'comics/single-comic.php', 'single-comic.php' ) );

	if ( '' === $default_template ) {
		add_filter( 'post_thumbnail_html', 'mangapress_disable_post_thumbnail', 500, 2 );
		add_filter( 'the_content', 'mangapress_single_comic_content_filter' );
		return $default_template;
	}

	return $default_template;
}


/**
 * Filter contents of single comic post
 *
 * @param string $content Post content.
 *
 * @return string
 * @since 2.9
 */
function mangapress_single_comic_content_filter( string $content ): string {
	global $post;

	if ( get_post_type( $post ) !== MangaPress_Posts::POST_TYPE ) {
		return $content;
	}

	$thumbnail_size = $image_sizes['comic-page'] ?? 'large';

	remove_filter( 'the_content', 'mangapress_single_comic_content_filter' );
	remove_filter( 'post_thumbnail_html', 'mangapress_disable_post_thumbnail' );

	ob_start();
	require MP_ABSPATH . 'templates/single-comic.php';
	$generated_content = ob_get_contents();
	ob_end_clean();

	return $generated_content . $content;
}



/**
 * Remove post thumbnail from Comic Posts since post thumbnails are already
 * assigned when the_content filter is run
 *
 * @param string $html Generated image html.
 * @param int    $post_id Post ID.
 *
 * @return string
 * @since 2.9
 */
function mangapress_disable_post_thumbnail( string $html, int $post_id ): string {
	if ( MangaPress_Posts::POST_TYPE === get_post_type( $post_id ) ) {
		return '';
	}

	return $html;
}


/**
 * Create a date-archive permalink for Comics (for monthly links)
 *
 * @param string $monthlink Existing link to be modified or replaced.
 * @param string $year Year portion of permalink.
 * @param string $month Month portion of permalink.
 *
 * @return string|void
 */
function mangapress_month_link( string $monthlink, string $year = '', string $month = '' ) {
	$posts = MangaPress_Bootstrap::get_instance()->get_helper( 'posts' );
	$slug  = $posts->get_slug();

	return home_url( "/{$slug}/{$year}/{$month}" );
}


/**
 * Create a date-archive permalink for Comics
 *
 * @param string $daylink Existing link to be modified or replaced.
 * @param string $year Year portion of permalink.
 * @param string $month Month portion of permalink.
 * @param string $day Day portion of permalink.
 *
 * @return string
 */
function mangapress_day_link( string $daylink, string $year = '', string $month = '', string $day = '' ): string {

	$posts = MangaPress_Bootstrap::get_instance()->get_helper( 'posts' );
	$slug  = $posts->get_slug();

	$relative = "/{$slug}/{$year}/{$month}/{$day}";

	return home_url( $relative );
}


/**
 * Echoes the current version of Manga+Press.
 * Replaces mpp_comic_version()
 *
 * @since 2.9
 * @return void
 */
function mangapress_version() {
	echo esc_attr( MP_VERSION );
}


/**
 * Set the post-type for get_boundary_post()
 * Workaround for issue #27094 {@link https://core.trac.wordpress.org/ticket/27094}
 *
 * @access private
 * @param WP_Query $query WordPress query object.
 * @return void
 */
function mangapress_set_post_type_for_boundary( $query ) {
	$query->set( 'post_type', 'mangapress_comic' );
}
