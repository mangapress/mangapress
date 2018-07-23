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
 * @deprecated Since 3.5
 * @since 2.9
 */
function mangapress_get_all_comics_for_archive($params = array())
{
    _deprecated_function(__FUNCTION__, MP_VERSION);
}

function mangapress_get_archive_style_template($style)
{
    if (in_array($style, ['list', 'gallery', 'calendar'])) {
        mangapress_get_template_part('content/archive', $style);
    } else {
        mangapress_get_template_part('content/archive', 'list');
    }
}
add_action('mangapress_archive_style_template', 'mangapress_get_archive_style_template');


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