<?php
/**
 * Manga+Press Template functions
 *
 * @todo Update docblocks
 *
 * @package Manga_Press
 * @subpackage Manga_Press_Template_Functions
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

/**
 * @param string $template
 *
 * @return string mixed
 */
function mangapress_template_loader($template)
{
    global $wp_query;
    if ( is_embed() ) {
        return $template;
    }

    $default = mangapress_get_default_template_file();
    if ( $default ) {

        $templates = mangapress_get_template_hierarchy($default);
        $template = locate_template($templates);

        if ( ! $template ) {
            $template = MP_ABSPATH . 'templates/' . $default;
        }
    }

    return $template;
}


/**
 * Get Manga+Press default templates
 *
 * @return string
 */
function mangapress_get_default_template_file()
{
    global $wp_query;

    if (is_singular(MangaPress_Posts::POST_TYPE)) {
        $template = 'single-comic.php';
    } elseif (is_post_type_archive(MangaPress_Posts::POST_TYPE) || is_comic_archive_page()) {
        $template = 'archive-comic.php';
    } elseif (is_latest_comic_page()) {
        $template = 'latest-comic.php';
    } else {
        $template = '';
    }

    return $template;
}

/**
 * Get Manga+Press template hierarchy
 * @param string $template
 *
 * @return array
 */
function mangapress_get_template_hierarchy($template)
{
    global $wp_query;

    $templates[] = 'mangapress.php';
    if (is_singular(MangaPress_Posts::POST_TYPE)) {
        $object = get_queried_object();
        $name_decoded = urldecode( $object->post_name );
        if ( $name_decoded !== $object->post_name ) {
            $templates[] = "comic/single-comic-{$name_decoded}.php";
            $templates[] = "single-comic-{$name_decoded}.php";
        }
        $templates[] = "comic/single-comic-{$object->post_name}.php";
        $templates[] = "single-comic-{$object->post_name}.php";
        $templates[] = "comic/single-comic.php";
        $templates[] = "single-comic.php";
    }

    if (is_post_type_archive(MangaPress_Posts::POST_TYPE) || is_comic_archive_page()) {
        $templates[] = 'comic/archive-comic.php';
        $templates[] = 'archive-comic.php';
    }

    if (is_latest_comic_page()) {
        // no object to query
        $templates[] = 'comic/latest-comic.php';
        $templates[] = 'latest-comic.php';
    }

    return $templates;
}

/**
 * Get a template part from the theme. If it doesn't exist, use one provided by the plugin
 * @param string $slug
 * @param string $name
 */
function mangapress_get_template_part($slug, $name = '')
{
    do_action( "mangapress_get_template_part_{$slug}", $slug, $name );

    $templates = array();
    $name = (string) $name;
    if ( '' !== $name )
        $templates[] = "{$slug}-{$name}.php";

    $templates[] = "{$slug}.php";

    $template = locate_template($templates);

    if ( $template ) {
        require $template;
    } else {
        require MP_ABSPATH . "templates/{$slug}-{$name}.php";
    }
}

/**
 * Modify loop and set up for Manga+Press — used only on Latest Comic and Comic Archives
 * @param \WP_Query $query
 */
function mangapress_pre_get_posts(\WP_Query $query)
{
    if (is_latest_comic_page()) {
        $query->set('post_type', MangaPress_Posts::POST_TYPE);
        $query->set('posts_per_page', 1);
    }

    if (is_comic_archive_page()) {
        $mp_options = MangaPress_Bootstrap::get_options();
        $order = $mp_options['basic']['archive_order'];
        $orderby = $mp_options['basic']['archive_orderby'];

        $query->set('order', $order);
        $query->set('orderby', $orderby);
        $query->set('posts_per_page', -1);
    }
}
add_action('pre_get_posts', 'mangapress_pre_get_posts');


/**
 * Get all comics for archives page
 * @deprecated Since 3.5
 * @since 2.9
 */
function mangapress_get_all_comics_for_archive($params = array())
{
    _deprecated_function(__FUNCTION__, MP_VERSION);
}

/**
 * Get the archive style template partial
 * @uses mangapress_archive_style_template action
 * @param string $style Archive style-type
 */
function mangapress_get_archive_style_template($style)
{
    if (in_array($style, ['list', 'gallery', 'calendar'])) {
        mangapress_get_template_part('content/archive', $style);
    } else {
        mangapress_get_template_part('content/archive', 'list');
    }
}
add_action('mangapress_archive_style_template', 'mangapress_get_archive_style_template');

/**
 * Open the article tag inside the loop. Used primarily on the archive-comic.php template
 * @uses mangapress_opening_article_tag filter
 * @param string $tag HTML tag. Defaults to article
 * @param array $params Array of parameters @todo document accepted parameters
 *
 * @return string
 */
function mangapress_opening_article_tag($tag, $params)
{
    $attr_string = '';
    if (isset($params['attr'])) {
        foreach ( $params['attr'] as $name => $value ) {
            $attr_string .= " $name=" . '"' . $value . '"';
        }
    }

    $classes = get_post_class();

    if (isset($params['style'])) {
        if (in_array($params['style'], ['list', 'gallery'])) {
            $tag = 'li';
        }

        $classes[] = 'mangapress-archive-' . $params['style'] . '-item';
        $attr_string .= ' class="' . join(' ', $classes) . '"';
    }

    $tag_string = "<$tag $attr_string>";

    return $tag_string;
}
add_filter('mangapress_opening_article_tag', 'mangapress_opening_article_tag', 10, 2);

/**
 * Close the article tag inside the loop. Used primarily on the archive-comic.php template
 * @uses mangapress_closing_article_tag filter
 * @param string $tag HTML tag. Defaults to article
 * @param array $params Array of parameters @todo document accepted parameters
 *
 * @return string
 */
function mangapress_closing_article_tag($tag, $params)
{
    if (isset($params['style'])) {
        if (in_array($params['style'], ['list', 'gallery'])) {
            $tag = 'li';
        }
    }

    $tag_string = "</$tag>";

    return $tag_string;
}
add_filter('mangapress_closing_article_tag', 'mangapress_closing_article_tag', 10, 2);

/**
 * Create a wrapper for the archive list. Used for the archive-comic.php template
 * @param string $style Archive style-type
 */
function mangapress_archive_style_opening_tag($style)
{
    $classes = [
        'mangapress-archive-feed'
    ];
    if (in_array($style, ['list', 'gallery'])) {
        $class = ' class="%s"';
        if ($style == 'gallery') {
            $classes[] = 'mangapress-archive-gallery';
            $class = sprintf($class, join(' ', $classes));
        }
        echo "<ul $class>";
    }
}
add_action('mangapress_archive_style_opening_tag', 'mangapress_archive_style_opening_tag');

/**
 * Close the for the archive list. Used for the archive-comic.php template
 * @uses mangapress_archive_style_opening_tag action
 * @param string $style Archive style-type
 */
function mangapress_archive_style_closing_tag($style)
{
    if (in_array($style, ['list', 'gallery'])) {
        echo '</ul>';
    }
}
add_action('mangapress_archive_style_closing_tag', 'mangapress_archive_style_closing_tag');


/**
 * Creates embedded style-sheet for Manga+Press Gallery archive
 *
 * @return string
 */
function mangapress_archive_gallery_style()
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