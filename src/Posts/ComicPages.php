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
        add_filter('use_block_editor_for_post_type', [$this, 'gutenberg_can_edit_post_type'], 20, 2);
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
                    // phpcs:disable
                    'menu_icon'          => 'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxODguNjQgMjE1LjU5Ij4KICA8dGl0bGU+bWFuZ2EtcHJlc3MtbWVudS1pY29uLS1jb21pYy1wYWdlczwvdGl0bGU+CiAgPGc+CiAgICA8cGF0aCBmaWxsPSJibGFjayIgZD0iTTE5My40Myw0OS4yM2ExODkuOTEsMTg5LjkxLDAsMCwwLTE4LjM1LTIxLDE4OSwxODksMCwwLDAtMjEtMTguMzVDMTQzLjIzLDEuOTIsMTM4LDEsMTM1LDFIMzAuNTJBMTYuODYsMTYuODYsMCwwLDAsMTMuNjgsMTcuODRWMTk5Ljc1YTE2Ljg3LDE2Ljg3LDAsMCwwLDE2Ljg0LDE2Ljg0aDE1NWExNi44NywxNi44NywwLDAsMCwxNi44NC0xNi44NFY2OC4zN0MyMDIuMzIsNjUuMzUsMjAxLjQsNjAuMDksMTkzLjQzLDQ5LjIzWm0tNC41OCwxNTAuNTJhMy40MiwzLjQyLDAsMCwxLTMuMzcsMy4zN2gtMTU1YTMuNDIsMy40MiwwLDAsMS0zLjM3LTMuMzdWMTcuODRhMy40MiwzLjQyLDAsMCwxLDMuMzctMy4zN0gxMzVWNjEuNjNhNi43NCw2Ljc0LDAsMCwwLDYuNzQsNi43NGg0Ny4xNloiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0xMy42OCAtMSkiLz4KICAgIDxwYXRoIGZpbGw9ImJsYWNrIiBkPSJNMTA4LDczLjM3YTgwLjkzLDgwLjkzLDAsMCwxLDIyLjcxLDMuMDgsNzAuMTEsNzAuMTEsMCwwLDEsMTguNiw4LjIyLDQxLjQ0LDQxLjQ0LDAsMCwxLDEyLjU1LDEyLjA5LDI2Ljc0LDI2Ljc0LDAsMCwxLDAsMjkuOSw0Mi43Myw0Mi43MywwLDAsMS0xMi41NSwxMi4yMSw2NS44OSw2NS44OSwwLDAsMS0xOC42LDguMjIsODQsODQsMCwwLDEtMjIuNzEsM2gtNXEtNi4xNywxMi41Ni0xMi42NywyMC4wOEE3MS4yOSw3MS4yOSwwLDAsMSw3My42NSwxODRxLTEwLjE2LDYuMjctMjQuMDgsNi4yOEEzOS42OCwzOS42OCwwLDAsMCw2NiwxNzkuNjFhNzcuNjcsNzcuNjcsMCwwLDAsMTAuNzMtMTQuNDlBODcuNTQsODcuNTQsMCwwLDAsODQsMTQ2Ljg2LDcxLjIxLDcxLjIxLDAsMCwxLDcwLjExLDE0MWE0OC44LDQ4LjgsMCwwLDEtMTEtOC4xQTM0Ljc1LDM0Ljc1LDAsMCwxLDUyLjA4LDEyM2EyNi40LDI2LjQsMCwwLDEtMi41MS0xMS4zLDI2LjczLDI2LjczLDAsMCwxLDQuNTctMTQuOTVBNDEuNDQsNDEuNDQsMCwwLDEsNjYuNjksODQuNjdhNzAuMTEsNzAuMTEsMCwwLDEsMTguNi04LjIyQTgwLjkzLDgwLjkzLDAsMCwxLDEwOCw3My4zN1ptMjcuMTYsMjUuNzlhMy41LDMuNSwwLDAsMC0uMjMtMi4xNyw3Ljg5LDcuODksMCwwLDAtMS4zNy0yLDYuNzYsNi43NiwwLDAsMC0yLjA1LTEuNiw1LjA2LDUuMDYsMCwwLDAtMi4yOC0uNTcsNCw0LDAsMCwwLTEuNi42OGMtLjc2LjQ2LTEuNzUsMS4xNC0zLDIuMDZzLTIuNjIsMi00LjIyLDMuNDJsLTUuNTksNC43OWMuMTUtLjYxLjMtMS4yOS40NS0yYTIyLjE0LDIyLjE0LDAsMCwxLC41Ny0yLjE3bC42OS0yYTE3LjY1LDE3LjY1LDAsMCwwLC41Ny0yLjE3Yy4xNS0uOTEuMy0xLjc1LjQ2LTIuNTFzLjMtMS40OS40NS0yLjE3YTkuMTMsOS4xMywwLDAsMCwuMjMtMS45NHYtMS42YTQsNCwwLDAsMC0uMTEtLjkxLDEuMTQsMS4xNCwwLDAsMC0uNDYtLjY5LDIuNjcsMi42NywwLDAsMC0uOTEtLjM0LDcuMjEsNy4yMSwwLDAsMC0yLjUxLDBsLTEuMTQuMjNhMTIuMTIsMTIuMTIsMCwwLDAtMS4yNi4zNCw1LjUxLDUuNTEsMCwwLDAtMS4zNy42OSwxMCwxMCwwLDAsMC0xLjM3LjU3LDQuMTMsNC4xMywwLDAsMC0xLjE0LjgsNS4yOCw1LjI4LDAsMCwwLS42OC44LDUuNjksNS42OSwwLDAsMC0uNDYuNzljLS4xNSwxLjM3LS4zMSwyLjYzLS40NiwzLjc3cy0uMjYsMi4yMS0uMzQsMy4yUzEwNiw5OC4yMSwxMDYsOTl2Mi4xN3EtMi43NC0yLjA1LTQuOTEtMy41M1Q5Ny41LDk1LjE2YTE2LjUyLDE2LjUyLDAsMCwwLTIuMjgtMS40OCw0LjI2LDQuMjYsMCwwLDAtMS4xNC0uNDYsMi4wOSwyLjA5LDAsMCwwLS45Mi4yM3EtLjQ1LjIyLTEsLjU3YTUuNDUsNS40NSwwLDAsMC0xLC44LDUuMzksNS4zOSwwLDAsMC0uOTEsMS4zN2MtLjMxLjQ2LS42MS44OC0uOTIsMS4yNmE0LjEyLDQuMTIsMCwwLDAtLjY4LDEuMjVjLS4xNS40Ni0uMy44OC0uNDYsMS4yNnMtLjMuNzItLjQ1LDFhMS43OSwxLjc5LDAsMCwwLS4yMy44di42OWExLjM1LDEuMzUsMCwwLDAsLjExLjU3LDEsMSwwLDAsMSwuMTIuNDYsMTMuNTksMTMuNTksMCwwLDAsMSwuOTEsOC40NCw4LjQ0LDAsMCwwLDEuNDkuOTFjLjYxLjMxLDEuMjkuNjksMiwxLjE0czEuNi45MiwyLjUxLDEuMzdsOCw0LjExTDkyLDExNy40MmMtMS4zNy43Ni0yLjU4LDEuNDgtMy42NSwyLjE3cy0yLDEuMjUtMi43NCwxLjcxYTguODEsOC44MSwwLDAsMC0xLjcxLDEuMjUsNC4yNCw0LjI0LDAsMCwwLS44LDEsMy40MiwzLjQyLDAsMCwwLC4xMiwxLjk0LDUuMzksNS4zOSwwLDAsMCwxLjE0LDEuOTQsNS4xOCw1LjE4LDAsMCwwLDEuODIsMS4zNyw1LjM4LDUuMzgsMCwwLDAsMi4xNy40Niw3LjkzLDcuOTMsMCwwLDAsMS4zNy0uMTIsNC44OSw0Ljg5LDAsMCwwLDEuMzctLjQ1LDE3LDE3LDAsMCwxLDEuNi0uNjksNy4xNCw3LjE0LDAsMCwwLDEuODItMWw1LjcxLTMuODhxLS45MiwyLjUyLTEuNzEsNC41N3QtMS4yNiwzLjU0Yy0uMywxLS41NywxLjc4LS44LDIuMzlhNS4zNiw1LjM2LDAsMCwwLS4zNCwxLjE0QTIuNDMsMi40MywwLDAsMCw5NiwxMzZhMS42NywxLjY3LDAsMCwwLDEuMjUsMS4zNyw1LjkzLDUuOTMsMCwwLDAsMS4zNy4xMSw5LjgzLDkuODMsMCwwLDAsMi44Ni0uNDUsMTEuNTIsMTEuNTIsMCwwLDAsMi43My0xLjI2LDguNzYsOC43NiwwLDAsMCwyLjE3LTEuOTQsNS4zNiw1LjM2LDAsMCwwLDEuMTQtMi41MWwzLjQzLTEyLjEsNC4xMSw0LjExcTEuODIsMS44MywzLDMuMDhhMTkuMjgsMTkuMjgsMCwwLDAsMS44MywxLjgzYy40Ni4zOC43Ni41Ny45MS41N2E0LDQsMCwwLDAsLjkxLS4xMSwyLjY1LDIuNjUsMCwwLDAsLjkyLS40Niw5LjMxLDkuMzEsMCwwLDAsLjkxLS44bC45MS0uOTFjLjMxLS40Ni41Ny0uODQuOC0xLjE0YTQuNDYsNC40NiwwLDAsMCwuNTctMWMuMTUtLjM4LjMxLS43Mi40Ni0xbC40Ni0uOTF2LS4zNGEuODMuODMsMCwwLDEsLjExLS4zNS43Ni43NiwwLDAsMCwuMTEtLjM0di0uMzRhNS40Miw1LjQyLDAsMCwwLS4yMi0xLjQ4LDEwLDEwLDAsMCwwLS41Ny0xLjQ5LDcuNjQsNy42NCwwLDAsMC0uOTItMS4zNyw5LjA5LDkuMDksMCwwLDAtMS40OC0xLjM3bC01LjI1LTQuMSw3LjE5LTQuOHEzLjA3LTIuMDUsNS4xMy0zLjU0YzEuMzctMSwyLjQtMS43OCwzLjA5LTIuMzlBNiw2LDAsMCwwLDEzNS4xNiw5OS4xNloiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0xMy42OCAtMSkiLz4KICA8L2c+Cjwvc3ZnPgo=',
                    // phpcs:enable
                    'show_ui'            => true,
                    'hierarchical'       => true,
                    'public'             => true,
                    'publicly_queryable' => true,
                    'supports'           => [
                        'title',
                        'thumbnail',
                        'editor',
                    ],
                    'rewrite'            => [
                        'slug' => 'comics',
                    ],
                    'show_in_nav_menus'  => true,
                    'show_in_rest'       => true,
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

    /**
     * Disable Gutenberg on Archive & Latest only
     *
     * @param boolean $can_edit
     * @param string $post_type
     * @return bool
     * @todo Explore the possibility of adding a setting to turn on the editor
     */
    public function gutenberg_can_edit_post_type($can_edit, $post_type)
    {
        if ($post_type === self::POST_TYPE) {
            $post_id = filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);
            $is_what = get_post_meta($post_id, 'comic_page__type', true);
            if (in_array($is_what, ['latest', 'archive'])) {
                remove_post_type_support(self::POST_TYPE, 'editor');
                return false;
            }
        }

        return $can_edit;
    }
}
