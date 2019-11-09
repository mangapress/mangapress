<?php
/**
 * Theme functions
 * @package MangaPress\Helpers\Theme_Functions
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\Theme\Functions;

use MangaPress\Posts\ComicPages;
use MangaPress\Posts\Comics;

/**
 * Run all related actions and filters
 */
function theme_init()
{
    add_filter('mangapress_opening_article_tag', '\MangaPress\Theme\Functions\opening_article_tag', 10, 2);
    add_filter('mangapress_closing_article_tag', '\MangaPress\Theme\Functions\closing_article_tag', 10, 2);

    add_action('mangapress_get_comic_header', '\MangaPress\Theme\Functions\get_comic_header');
    add_action('mangapress_get_comic_footer', '\MangaPress\Theme\Functions\get_comic_footer');

    add_action('mangapress_article_header', '\MangaPress\Theme\Functions\article_header');

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

//    add_action('mangapress_output_no_comics_message', [$this, 'no_comics_message']);
    add_action('mangapress_after_article_content', 'MangaPress\Theme\Functions\comic_page__after_article_content');
}

/**
 * Load comic page header
 * @uses mangapress_get_comic_header
 */
function get_comic_header()
{
    get_template_part('header', 'comic');
}

/**
 * Load comic page footer
 * @uses mangapress_get_comic_footer
 */
function get_comic_footer()
{
    get_template_part('footer', 'comic');
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
    if (file_exists(MP_ABSPATH . "/resources/templates/{$slug}-{$name}.php")) {
        require MP_ABSPATH . "/resources/templates/{$slug}-{$name}.php";
    } else {
        require $template;
    }
}

/**
 * Get the archive style template partial
 * @param string $style Archive style-type
 * @uses mangapress_archive_style_template
 */
function get_archive_style_template($style)
{
    if (in_array($style, ['list', 'gallery', 'calendar'])) {
        get_template_part('content/archive', $style);
    } else {
        get_template_part('content/archive', 'list');
    }
}

/**
 * Open the article tag inside the loop. Used primarily on the archive-comic.php template
 * @param string $tag HTML tag. Defaults to article
 * @param array $params Array of parameters @todo document accepted parameters
 *
 * @return string
 * @uses mangapress_opening_article_tag
 */
function opening_article_tag($tag, $params = [])
{
    $attr_string = '';
    if (isset($params['attr'])) {
        foreach ($params['attr'] as $name => $value) {
            $attr_string .= " $name=" . '"' . $value . '"';
        }
    }

    $classes = get_post_class() ? get_post_class() : 'entry';

    if (isset($params['style'])) {
        if (in_array($params['style'], ['list', 'gallery'])) {
            $tag = 'li';
        }

        $classes[]   = 'mangapress-archive-' . $params['style'] . '-item';
    }

    $attr_string .= ' class="' . join(' ', $classes) . '"';

    $tag_string = "<$tag $attr_string>";

    return $tag_string;
}

/**
 * Close the article tag inside the loop. Used primarily on the archive-comic.php template
 * @param string $tag HTML tag. Defaults to article
 * @param array $params Array of parameters @todo document accepted parameters
 *
 * @return string
 * @uses mangapress_closing_article_tag
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
 * after_article_content
 * Runs on Comic Pages after output of page content
 *
 * @param \WP_Post $post
 */
function comic_page__after_article_content($post = null)
{
    if (get_post_type($post) !== ComicPages::POST_TYPE) {
        return; // exit if post-type doesn't match
    }

    /**
     * mangapress_comic_page_content
     * Outputs content and templates for Comic Pages
     * @param \WP_Post $post WordPress post object
     * @since 4.0.0
     */
    do_action('mangapress_comic_page_content', $post);
}

/**
 * Create a wrapper for the archive list. Used for the archive-comic.php template
 * @param string $style Archive style-type
 * @param string $tag Opening tag
 */
function archive_style_opening_tag($style, $tag = 'article')
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

        $post_classes = get_post_class();

        echo "<$tag class=\"" . join(' ', $post_classes) . "\">";
        echo "<div class=\"entry-content\">";
        echo "<ul $class>";
    }
}

/**
 * Close the for the archive list. Used for the archive-comic.php template
 * @param string $style Archive style-type
 * @param string $tag
 * @uses mangapress_archive_style_opening_tag action
 */
function archive_style_closing_tag($style, $tag = 'article')
{
    if (in_array($style, ['list', 'gallery'])) {
        echo '</div>';
        echo '</ul>';
        echo "</$tag>";
    }
}

/**
 * Output article header for pages and single comics
 * @param \WP_Post $post WordPress post object
 */
function article_header($post)
{
    ?>
    <header class="entry-header">
        <h1 class="entry-title"><?php echo apply_filters('the_title', get_post_field('post_title', $post)) ?></h1>
    </header>
    <?php
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
    $single_comic_query = new \WP_Query(
        [
            'post_type'      => 'mangapress_comic',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'order'          => 'DESC',
            'orderby'        => 'date',
        ]
    );

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

    /**
     * latest_comic_start
     * Runs before latest comic is retrieved
     * @since 2.9
     */
    do_action('latest_comic_start');
    $wp_query = get_latest_comic();
    if ($wp_query->found_posts == 0) {
        /**
         * the_latest_comic_content_error
         * Outputs error message if no comics are found
         * @param string $message Message or template to output
         * @since 2.9
         */
        apply_filters(
            'the_latest_comic_content_error',
            __('<p class="error">No comics was found.</p>', MP_DOMAIN)
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
    /**
     * latest_comic_end
     * Runs after latest comic is retrieved but before $wp_query is reset
     * @since 2.9
     */
    do_action('latest_comic_end');
    wp_reset_query();
}

/**
 * Get adjacent comic
 *
 * @param bool $previous
 * @param bool $group_by
 * @param bool $group_by_parent
 * @param string $taxonomy
 *
 * @return \WP_Post|false
 */
function get_adjacent_comic(
    $previous = true,
    $group_by = false,
    $group_by_parent = false,
    $taxonomy = Comics::TAX_SERIES
) {
    global $post;

    $order     = $previous ? 'DESC' : 'ASC';
    $direction = $previous ? 'before' : 'after';

    $args = [
        'post_not__in'   => $post->ID,
        'post_type'      => Comics::POST_TYPE,
        'posts_per_page' => 1,
        'order'          => $order,
        'orderby'        => 'date',
        'date_query'     => [
            $direction => $post->post_date,
        ],
    ];

    $args = get_group_by_args($args, $taxonomy, $group_by, $group_by_parent);

    /**
     * mangapress_get_adjacent_comic_args
     * @param array $args
     * @param boolean $previous
     *
     * @since 4.0.0
     */
    $args = apply_filters('mangapress_get_adjacent_comic_args', $args, $previous);

    $posts = get_posts($args);

    if (!isset($posts[0])) {
        return false;
    }

    return $posts[0];
}

/**
 * Get boundary comic
 *
 * @param bool $start
 * @param bool $group_by
 * @param bool $group_by_parent
 * @param string $taxonomy
 * @return bool|\WP_Post
 */
function get_boundary_comic($start = true, $group_by = false, $group_by_parent = false, $taxonomy = Comics::TAX_SERIES)
{
    global $post;

    $order = $start ? 'ASC' : 'DESC';

    $args = [
        'post_not__in'   => [$post->ID],
        'post_type'      => Comics::POST_TYPE,
        'posts_per_page' => 1,
        'order'          => $order,
        'orderby'        => 'date',
    ];

    $args = get_group_by_args($args, $taxonomy, $group_by, $group_by_parent);
    /**
     * mangapress_get_boundary_comic_args
     * @param array $args
     * @param boolean $start
     *
     * @since 4.0.0
     */
    $args = apply_filters('mangapress_get_boundary_comic_args', $args, $start);

    /**
     * @var \WP_Post[] $posts
     */
    $posts = get_posts($args);

    if (!isset($posts[0]) || $post->ID === $posts[0]->ID) {
        return false;
    }

    return $posts[0];
}

/**
 * Get group-by arguments
 *
 * @param array $args
 * @param string $taxonomy
 * @param bool $group_by
 * @param bool $group_by_parent
 * @return array
 */
function get_group_by_args($args, $taxonomy, $group_by = false, $group_by_parent = false)
{
    global $post;

    if ($group_by) {
        /**
         * @var \WP_Term[] $terms
         */
        $terms = wp_get_object_terms(
            [$post->ID],
            $taxonomy,
            ['orderby' => 'parent', 'order' => 'DESC', 'fields' => 'all']
        );

        if (!empty($terms)) {
            if ($group_by_parent) {
                $terms = array_reverse($terms);
            }

            $args['tax_query'] = [
                'relation' => 'OR',
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => [$terms[0]->term_id],
                ],
            ];
        }
    }

    return $args;
}
