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

    if (in_array($comicarchive_page_style, array('list', 'gallery', 'calendar'))) {
        $template = locate_template(array(
            "comics/comic-archive-{$comicarchive_page_style}.php",
            'comics/comic-archive.php',
        ));
    } else {
        $template = locate_template(array(
            'comics/comic-archive.php',
        ));
    }

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
