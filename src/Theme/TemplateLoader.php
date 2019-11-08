<?php
/**
 * @package MangaPress\Theme\TemplateLoader
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\Theme;

use MangaPress\Options\Options;
use MangaPress\PluginComponent;

/**
 * Class TemplateLoader
 * @package MangaPress\Theme
 */
class TemplateLoader implements PluginComponent
{
    /**
     * Run template loader
     */
    public function init()
    {
        add_filter('template_include', [$this, 'template_loader']);
        add_action('pre_get_posts', [$this, 'pre_get_posts']);
    }

    /**
     * Load Manga+Press templates
     * @param string $template
     * @return string
     * @global \WP_Query $wp_query
     */
    public function template_loader($template)
    {
        global $wp_query;
        if (is_embed()) {
            return $template;
        }

        $default = $this->get_default_template_file();
        if ($default) {
            $templates = $this->get_template_hierarchy($default);
            $template  = locate_template($templates);

            if (!$template) {
                $template = MP_ABSPATH . 'resources/templates/' . $default;
            }
        }

        return $template;
    }

    /**
     * Get Manga+Press default templates
     *
     * @return string
     */
    public function get_default_template_file()
    {
        if (is_comic()) {
            $template = 'single-comic.php';
        } elseif (is_comic_archive_page() && !is_date()) {
            $template = 'archive-comic.php';
        } elseif (is_latest_comic_page()) {
            $template = 'page-latest-comic.php';
        } elseif (is_comic_page()) {
            $template = 'page-comic.php';
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
    public function get_template_hierarchy($template)
    {
        global $wp_query;

        $templates[] = 'mangapress.php';
        if (is_comic()) {
            $object       = get_queried_object();
            $name_decoded = urldecode($object->post_name);
            if ($name_decoded !== $object->post_name) {
                $templates[] = "comic/single-comic-{$name_decoded}.php";
                $templates[] = "single-comic-{$name_decoded}.php";
            }
            $templates[] = "comic/single-comic-{$object->post_name}.php";
            $templates[] = "single-comic-{$object->post_name}.php";
            $templates[] = "comic/single-comic.php";
            $templates[] = "single-comic.php";
        }

        if (is_comic_archive_page()) {
            $templates[] = 'comic/archive-comic.php';
            $templates[] = 'archive-comic.php';
        }

        return $templates;
    }

    /**
     * Modify loop and set up for Manga+Press — used only on Latest Comic and Comic Archives
     * @param \WP_Query $query
     */
    public function pre_get_posts(\WP_Query $query)
    {
        if (is_admin()) {
            return;
        }

        if ($query->is_main_query() && is_comic_archive_page()) {
            $order   = Options::get_option('archive_order', 'basic');
            $orderby = Options::get_option('archive_orderby', 'basic');

            $query->set('order', $order);
            $query->set('orderby', $orderby);
            $query->set('posts_per_page', -1);
        }
    }
}
