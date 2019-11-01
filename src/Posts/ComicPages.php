<?php


namespace MangaPress\Posts;

use MangaPress\ContentTypes\ContentTypeRegistry;
use MangaPress\ContentTypes\PostType;
use MangaPress\PluginComponent;

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
}
