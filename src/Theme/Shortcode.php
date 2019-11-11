<?php
/**
 * @package MangaPress\Theme\Shortcode
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\Theme;

use MangaPress\PluginComponent;

/**
 * Class Shortcode
 * @package MangaPress\Theme
 */
class Shortcode implements PluginComponent
{
    public function init()
    {
        add_shortcode('latest-comic', [$this, 'latest_comic_shortcode']);
    }

    /**
     * Shortcode handler
     * @param array $attrs
     * @return string
     */
    public function latest_comic_shortcode($attrs = [])
    {
        $args = [
            'post_type'      => 'mangapress_comic',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'order'          => 'DESC',
            'orderby'        => 'date',
        ];

        $comics = new \WP_Query($args);
        if ($comics->found_posts == 0) {
            return __('No comics found', MP_DOMAIN);
        }

        global $post;
        $old  = $post;
        $post = $comics->post;
        setup_postdata($comics->post);

        ob_start();
        $shortcode_template = locate_template(['comics/latest-shortcode.php'], false, false);
        if (!$shortcode_template) {
            require_once MP_ABSPATH . 'resources/templates/content/latest-shortcode.php';
        } else {
            require_once $shortcode_template;
        }

        $content = ob_get_contents();
        ob_end_clean();
        wp_reset_postdata();
        $post = $old;

        return $content;
    }
}
