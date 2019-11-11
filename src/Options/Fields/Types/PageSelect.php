<?php
/**
 * Build a select field specifically for pages
 *
 * @package MangaPress\Options\Fields\Types
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\Options\Fields\Types;

use MangaPress\Posts\ComicPages;

/**
 * Class PageSelect
 * @package MangaPress\Options\Fields\Types
 */
class PageSelect extends Select
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $pages['pages']['title'] = __('Pages', MP_DOMAIN);
        $pages['pages']['pages'] = get_pages();

        $comic_pages = get_pages(
            ['post_type' => ComicPages::POST_TYPE, 'post_status' => ['publish', 'draft']]
        );

        if ($comic_pages) {
            $post_type_obj = get_post_type_object(ComicPages::POST_TYPE);

            $pages[ComicPages::POST_TYPE]['title'] = $post_type_obj->label;
            $pages[ComicPages::POST_TYPE]['pages'] = $comic_pages;
        }

        $options = array_merge([], ['no_val' => __('Select a Page', MP_DOMAIN)]);

        if (!isset($pages[ComicPages::POST_TYPE])) {
            foreach ($pages as $page) {
                $options[$page->ID] = esc_html($page->post_title);
            }
        } else {
            foreach ($pages as $type => $page) {
                $options[$type]['title'] = $page['title'];
                /**
                 * @var \WP_Post $p
                 */
                foreach ($page['pages'] as $p) {
                    $options[$type]['pages'][$p->ID] = esc_html($p->post_title)
                                                       . (
                                                       $p->post_status === 'draft'
                                                           ? '(' . esc_html($p->post_status) . ')' : ''
                                                       );
                }
            }
        }

        $this->options = $options;
    }
}
