<?php
/**
 * mangapress
 *
 * @package latestcomic-template-handlers
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */

/**
 * Template handler for Latest Comic end-point
 *
 * @global WP $wp
 * @param string $template
 * @return string
 */
function mangapress_latestcomic_template($template)
{
    global $wp;

    if (!$wp->did_permalink) {
        return $template;
    }

    if (strpos($wp->matched_rule, 'latest-comic') !== false) {
        $template = locate_template(array('comics/latest-comic.php'));
        return $template;
    }

    return $template;
}


/**
 * Template handler for Latest Comic page
 *
 * @param string $template Default template if requested template is not found
 * @return string
 */
function mangapress_latestcomic_page_template($template)
{
    if (!mangapress_is_queried_page('latestcomic_page')) {
        return $template;
    }

    $template = locate_template(array('comics/latest-comic.php'));

    // if template can't be found, then look for query defaults...
    if (!$template) {
        add_filter('the_content', 'mangapress_add_comic_to_latestcomic_page');
        return get_page_template();
    } else {
        return $template;
    }
}


/**
 * Add Latest Comic to page content
 *
 * @global WP_Post $post WordPress post object
 * @param string $content Post content being filtered
 * @return string
 */
function mangapress_add_comic_to_latestcomic_page($content)
{
   global $post;

    if (!mangapress_is_queried_page('latestcomic_page')) {
        return $content;
    }

    $image_sizes = get_intermediate_image_sizes();
    $wp_query    = mangapress_get_latest_comic();

    if (!$wp_query){
        return apply_filters(
            'the_latest_comic_content_error',
            '<p class="error">No recent comics were found.</p>'
        );
    }

    $thumbnail_size = isset($image_sizes['comic-page'])
                        ? $image_sizes['comic-page'] : 'large';

    $post = $wp_query->posts[0];

    setup_postdata($post);

    ob_start();
    require mangapress_get_content_template('latestcomic_page');
    $content = ob_get_clean();

    wp_reset_query();

    return apply_filters('the_latest_comic_content', $content);
}

