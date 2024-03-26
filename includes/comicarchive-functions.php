<?php
/**
 * mangapress
 *
 * @package comicarchive-functions
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */

/**
 * Get all comics for archives page
 *
 * @param array $params Parameters.
 *
 * @return \WP_Query
 * @since 2.9
 */
function mangapress_get_all_comics_for_archive( array $params = array() ): WP_Query {
	$mp_options   = MangaPress_Bootstrap::get_instance()->get_options();
	$order_params = array(
		'order'   => $mp_options['basic']['archive_order'],
		'orderby' => $mp_options['basic']['archive_orderby'],
	);

	if ( empty( $params ) ) {
		$params = array(
			'post_type'      => 'mangapress_comic',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		);
	}
	$params = array_merge( $params, $order_params );

	return new WP_Query( $params );
}

/**
 * Creates embedded style-sheet for Manga+Press Gallery archive
 *
 * @return string
 */
function mangapress_archive_gallery_style(): string {
	$styles = '
<style>
    .mangapress-archive-gallery {
        font-size: 0;
    }

    .mangapress-archive-gallery > li {
        text-align: center;
        width: 125px;
        min-height: 200px;
        font-size: 12px;
        list-style: none;
        margin: 10px;
        float: left;
    }

    .mangapress-archive-gallery > li:after {
         visibility: hidden;
         display: block;
         font-size: 0;
         content: " ";
         clear: both;
         height: 0;
    }

    .mangapress-archive-gallery .archive-item img {
        display: inline-block;
    }

    .comic-title-caption,
    .comic-post-date {
        text-align: center;
        margin: 0;
        padding: 0;
    }

    .comic-title-caption {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
</style>';

	/**
	 * Filter embedded stylesheet string
	 *
	 * @param string $styles
	 * @return string
	 */
	return apply_filters( 'mangapress_archive_gallery_style', $styles );
}
