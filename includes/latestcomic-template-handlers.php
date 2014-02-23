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
 * Handles display for the latest comic page.
 *
 * @global WP_Post $post WordPress Post
 *
 * @since 2.7
 * @param string $content. Post content
 * @return string
 */
function mpp_filter_latest_comic($content)
{
    global $post;    

    if (!($post->post_name == $mp_options['basic']['latestcomic_page'])) {
        return $content;
    }

    $image_sizes = get_intermediate_image_sizes();
    $wp_query = mpp_get_latest_comic();

    if (!$wp_query){
        return apply_filters(
            'the_latest_comic_content_error',
            '<p class="error">No Latest Comic was found.</p>'
        );
    }

    $thumbnail_size = isset($image_sizes['comic-page']) 
                        ? $image_sizes['comic-page'] : 'large';
    
    $post = $wp_query->posts[0];

    setup_postdata($post);
    
    $template_file_found = locate_template(array('templates/content/latest-comic.php'));
    $file = $template_file_found
                ? $template_file_found : MP_ABSPATH . 'templates/content/latest-comic.php';

    ob_start();
    require $file;
    $content = ob_get_clean();    
    
    wp_reset_query();
    
    return apply_filters('the_latest_comic_content', $content);
}


/**
 * mpp_latest_comic_page()
 * Set Latest Comic page template
 *
 * @global WP_Query $wp_query
 *
 * @since 2.7
 * @param string $template
 * @return string|void
 */
function mpp_latest_comic_page($template)
{
    if (!mpp_is_queried_page('latestcomic_page')) {
        return $template;
    }

    $template = locate_template(array('comics/latest-comic.php'));

    // if template can't be found, then look for query defaults...
    if (!$template) {
        add_filter('the_content', 'mpp_filter_latest_comic');
        return get_page_template();
    } else {
        return $template;
    }

}
