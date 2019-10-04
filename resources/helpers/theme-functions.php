<?php
/**
 * Template functions
 */
namespace MangaPress\Theme\Functions;

/**
 * Run all related actions and filters
 */
function theme_init()
{
    add_filter('mangapress_opening_article_tag', '\MangaPress\Theme\Functions\opening_article_tag', 10, 2);
    add_filter('mangapress_closing_article_tag', '\MangaPress\Theme\Functions\closing_article_tag', 10, 2);

    add_action(
        'mangapress_archive_style_template',
        '\MangaPress\Theme\Functions\get_archive_style_template'
    );

    add_action(
        'mangapress_archive_style_opening_tag',
        '\MangaPress\Theme\Functions\archive_style_opening_tag'
    );

    add_action(
        'mangapress_archive_style_closing_tag',
        '\MangaPress\Theme\Functions\archive_style_closing_tag'
    );
}

/**
 * Get a template part from the theme. If it doesn't exist, use one provided by the plugin
 * @param string $slug
 * @param string $name
 */
function get_template_part($slug, $name = '')
{
    do_action("mangapress_get_template_part_{$slug}", $slug, $name);

    $templates = [];
    $name      = (string)$name;
    if ('' !== $name) {
        $templates[] = "{$slug}-{$name}.php";
    }

    $templates[] = "{$slug}.php";

    $template = locate_template($templates);

    if ($template) {
        require $template;
    } else {
        require MP_ABSPATH . "templates/{$slug}-{$name}.php";
    }
}

/**
 * Get the archive style template partial
 * @param string $style Archive style-type
 * @uses mangapress_archive_style_template action
 */
function get_archive_style_template($style)
{
    if (in_array($style, ['list', 'gallery', 'calendar'])) {
        mangapress_get_template_part('content/archive', $style);
    } else {
        mangapress_get_template_part('content/archive', 'list');
    }
}

/**
 * Open the article tag inside the loop. Used primarily on the archive-comic.php template
 * @param string $tag HTML tag. Defaults to article
 * @param array $params Array of parameters @todo document accepted parameters
 *
 * @return string
 * @uses mangapress_opening_article_tag filter
 */
function opening_article_tag($tag, $params)
{
    $attr_string = '';
    if (isset($params['attr'])) {
        foreach ($params['attr'] as $name => $value) {
            $attr_string .= " $name=" . '"' . $value . '"';
        }
    }

    $classes = get_post_class();

    if (isset($params['style'])) {
        if (in_array($params['style'], ['list', 'gallery'])) {
            $tag = 'li';
        }

        $classes[]   = 'mangapress-archive-' . $params['style'] . '-item';
        $attr_string .= ' class="' . join(' ', $classes) . '"';
    }

    $tag_string = "<$tag $attr_string>";

    return $tag_string;
}

/**
 * Close the article tag inside the loop. Used primarily on the archive-comic.php template
 * @param string $tag HTML tag. Defaults to article
 * @param array $params Array of parameters @todo document accepted parameters
 *
 * @return string
 * @uses mangapress_closing_article_tag filter
 */
function closing_article_tag($tag, $params)
{
    if (isset($params['style'])) {
        if (in_array($params['style'], ['list', 'gallery'])) {
            $tag = 'li';
        }
    }

    $tag_string = "</$tag>";

    return $tag_string;
}


/**
 * Create a wrapper for the archive list. Used for the archive-comic.php template
 * @param string $style Archive style-type
 */
function archive_style_opening_tag($style)
{
    $classes = [
        'mangapress-archive-feed',
    ];
    if (in_array($style, ['list', 'gallery'])) {
        $class = ' class="%s"';
        if ($style == 'gallery') {
            $classes[] = 'mangapress-archive-gallery';
        } else {
            $classes[] = 'mangapress-archive-list';
        }
        $class = sprintf($class, join(' ', $classes));
        echo "<ul $class>";
    }
}

/**
 * Close the for the archive list. Used for the archive-comic.php template
 * @param string $style Archive style-type
 * @uses mangapress_archive_style_opening_tag action
 */
function archive_style_closing_tag($style)
{
    if (in_array($style, ['list', 'gallery'])) {
        echo '</ul>';
    }
}


/**
 * Creates embedded style-sheet for Manga+Press Gallery archive
 *
 * @return string
 */
function archive_gallery_style()
{
    $styles = "
<style type=\"text/css\">
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
         content: \" \";
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
</style>";


    /**
     * Filter embedded stylesheet string
     *
     * @param string $styles
     * @return string
     */
    return apply_filters('mangapress_archive_gallery_style', $styles);
}

/**
 * Retrieves the most recent comic
 *
 * @return \WP_Query
 * @since 2.7.2
 */
function get_latest_comic()
{
    $single_comic_query = new \WP_Query([
        'post_type'      => 'mangapress_comic',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'order'          => 'DESC',
        'orderby'        => 'date',
    ]);
    $single_comic_query->is_post_type_archive = false;
    return $single_comic_query;
}

/**
 * Start a Latest Comic loop
 * @return void
 * @global \WP_Query $wp_query
 * @since 2.9
 */
function start_latest_comic()
{
    global $wp_query;
    do_action('latest_comic_start');
    $wp_query = get_latest_comic();
    if ($wp_query->found_posts == 0) {
        apply_filters(
            'the_latest_comic_content_error',
            '<p class="error">No comics was found.</p>'
        );
    }
}

/**
 * End Latest Comic loop
 * @return void
 * @global WP_Query $wp_query
 * @since 2.9
 */
function end_latest_comic()
{
    global $wp_query;
    do_action('latest_comic_end');
    wp_reset_query();
}
