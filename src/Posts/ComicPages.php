<?php


namespace MangaPress\Posts;

use MangaPress\ContentTypes\ContentTypeRegistry;
use MangaPress\ContentTypes\PostType;
use MangaPress\PluginComponent;

/**
 * Class ComicPages
 * @package MangaPress\Posts
 */
class ComicPages implements PluginComponent, ContentTypeRegistry
{
    const POST_TYPE = 'mangapress_comicpage';

    /**
     * @var PostType $post_type
     */
    protected $post_type;

    /**
     * Initialize component
     */
    public function init()
    {
        $this->register_content_types();

        add_action('display_post_states', [$this, 'display_post_states'], 20, 2);
    }

    /**
     * Register content-types
     */
    public function register_content_types()
    {
        $this->post_type = new PostType(
            [
                'name'         => self::POST_TYPE,
                'textdomain'   => MP_DOMAIN,
                'label_plural' => __('Comic Pages', MP_DOMAIN),
                'label_single' => __('Comic Pages', MP_DOMAIN),
                'arguments'    => [
                    'show_ui'            => WP_DEBUG,
                    'hierarchical'       => true,
                    'public'             => true,
                    'publicly_queryable' => true,
                    'supports'           => [
                        'title',
                    ],
                    'rewrite'            => [
                        'slug' => 'comics',
                    ],
                    'show_in_nav_menus'  => true,
                    'taxonomies'         => [
                        Comics::TAX_SERIES,
                    ],
                ],
            ]
        );
    }

    /**
     * Add to statuses to indicate what pages do what
     *
     * @param string[] $post_statuses Array of post status
     * @param \WP_Post $post Current post in the loop
     *
     * @return string[]
     */
    public function display_post_states($post_statuses, $post)
    {
        if (!is_admin() || get_post_type($post) !== ComicPages::POST_TYPE) {
            return $post_statuses;
        }

        $is_what = get_post_meta($post->ID, 'comic_page__type', true);
        if ($is_what === 'latest') {
            $post_statuses[] = __('Latest Comic Page', MP_DOMAIN);
        }

        if ($is_what === 'archive') {
            $post_statuses[] = __('Comic Archive Page', MP_DOMAIN);
        }

        return $post_statuses;
    }
}
