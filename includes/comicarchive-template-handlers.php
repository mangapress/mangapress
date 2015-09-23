<?php
/**
 * mangapress
 *
 * @package comicarchive-template-handlers
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */

function mangapress_get_comicarchive_template($style)
{
    $fields = MangaPress_Bootstrap::get_instance()->get_helper('options')->options_fields();
    $options = $fields['basic']['comicarchive_page_style'];
    unset($options['value']['no_val']); // remove this, we don't need it

    if (!in_array($style, array_keys($options['value']))) {
        return 'comic-archive-list.php'; // return the default if the value doesn't match
    }

    return "comic-archive-{$style}.php";
}


/**
 * Template handler for Comic Archive end-point
 *
 * @global WP $wp
 * @param string $template
 * @return string
 */
function mangapress_comicarchive_template($template)
{
    global $wp;

    if (!$wp->did_permalink) {
        return $template;
    }

    if (strpos($wp->matched_rule, 'past-comics') !== false) {
        $comicarchive_page_style = MangaPress_Bootstrap::get_instance()->get_option('basic', 'comicarchive_page_style');
        $archive_template = mangapress_get_comicarchive_template($comicarchive_page_style);

        return locate_template(array("comics/{$archive_template}", 'comics/past-comics.php'));
    }

    return $template;
}


/**
 * Template handler for Comic Archive page
 *
 * @param string $default_template Default template if requested template is not found
 * @return string
 */
function mangapress_comicarchive_page_template($default_template)
{
    if (!mangapress_is_queried_page('comicarchive_page')) {
        return $default_template;
    }

    // maintain template hierarchy if not page.php or index.php
    if (!in_array(basename($default_template), array('page.php', 'index.php'))) {
        return $default_template;
    }

    $comicarchive_page_style = MangaPress_Bootstrap::get_instance()->get_option('basic', 'comicarchive_page_style');
    $archive_template = mangapress_get_comicarchive_template($comicarchive_page_style);

    $template = locate_template(array("comics/$archive_template"));

    // if template can't be found, then look for query defaults...
    if (!$template) {
        add_filter('the_content', 'mangapress_create_comicarchive_page');
        return $default_template;
    } else {
        return $template;
    }
}


/**
 * Add comic archive output to Comic Archive page content
 *
 * @access private
 * @param string $content Page content being filtered
 * @return string
 */
function mangapress_create_comicarchive_page($content)
{
    global $post, $wp_query;

    if (!mangapress_is_queried_page('comicarchive_page')) {
        return $content;
    }
    $old_query = $wp_query;
    $wp_query = mangapress_get_all_comics_for_archive();

    if (!$wp_query){
        return apply_filters(
            'the_comicarchive_content_error',
            '<p class="error">No comics were found.</p>'
        );
    }

    $comicarchive_page_style = MangaPress_Bootstrap::get_instance()->get_option('basic', 'comicarchive_page_style');

    ob_start();
    require mangapress_get_content_template($comicarchive_page_style);
    $content = ob_get_clean();

    $wp_query = $old_query;

    wp_reset_query();

    return apply_filters('the_comicarchive_content', $content);

}


/**
 * Filter for hooking into mangapress_comic-specific WP_Query object
 *
 * @param WP_Query $query
 * @return \WP_Query
 */
function mangapress_pre_get_posts(WP_Query $query)
{
    if (!did_action('_mangapress_pre_archives_get_posts')) {
        return $query;
    }
}
add_filter('pre_get_posts', 'mangapress_pre_get_posts');