<?php


namespace MangaPress\Posts;

use MangaPress\ContentTypes\ContentTypeRegistry;
use MangaPress\PluginComponent;
use MangaPress\ContentTypes\Taxonomy;
use MangaPress\ContentTypes\PostType;
use MangaPress\Options\Options;

/**
 * Class Posts
 * @package MangaPress\Posts
 */
class Comics implements PluginComponent, ContentTypeRegistry
{


    /**
     * Post-type name
     *
     * @var string
     */
    const POST_TYPE = 'mangapress_comic';


    /**
     * Taxonomy name for Series
     *
     * @var string
     */
    const TAX_SERIES = 'mangapress_series';


    /**
     * Default archive date format
     *
     * @var string
     */
    const COMIC_ARCHIVE_DATEFORMAT = 'm.d.Y';
    /**
     * Post-type Slug. Defaults to comic.
     *
     * @var string
     */
    protected $slug = 'comic';
    /**
     * Comic Archives slug
     *
     * @var string
     */
    protected $archive_slug = 'comic-archives';
    /**
     * Latest Comic slug
     *
     * @var string
     */
    protected $latest_comic_slug = 'latest-comic';
    /**
     * Class for initializing custom post-type
     *
     * @var PostType
     */
    private $post_type = null;

    public function init()
    {
        $this->register_content_types();

        add_action('init', [$this, 'rewrite_rules']);

        // Setup Manga+Press Post Options box
        add_action('wp_ajax_' . Actions::ACTION_INSERT_COMIC, [$this, 'get_image_html_ajax']);
        add_action('wp_ajax_' . Actions::ACTION_REMOVE_COMIC, [$this, 'get_image_html_ajax']);
        add_action('wp_ajax_' . Actions::ACTION_INSERT_COVER, [$this, 'get_image_html_ajax']);
        add_action('wp_ajax_' . Actions::ACTION_REMOVE_COVER, [$this, 'get_image_html_ajax']);
        add_action('save_post_mangapress_comic', [$this, 'save_post'], 500, 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

        /*
         * Actions and filters for modifying our Edit Comics page.
         */
        add_action('manage_posts_custom_column', [$this, 'comics_headers']);
        add_filter('manage_edit-mangapress_comic_columns', [$this, 'comics_columns']);
    }


    /**
     * Register the post-type
     *
     * @return void
     */
    public function register_content_types()
    {
        // register taxonomy
        $taxonomy = new Taxonomy(
            [
                'name'         => self::TAX_SERIES,
                'textdomain'   => MP_DOMAIN,
                'label_single' => __('Series', MP_DOMAIN),
                'label_plural' => __('Series', MP_DOMAIN),
                'objects'      => [self::POST_TYPE],
                'arguments'    => [
                    'hierarchical'          => true,
                    'query_var'             => 'series',
                    'rewrite'               => [
                        'slug' => 'series',
                    ],
                    'show_in_rest'          => true,
                    'rest_base'             => 'series',
                    'rest_controller_class' => 'WP_REST_Terms_Controller',
                ],
            ]
        );

        $this->post_type = new PostType(
            [
                'name'         => self::POST_TYPE,
                'textdomain'   => MP_DOMAIN,
                'label_plural' => __('Comics', MP_DOMAIN),
                'label_single' => __('Comic', MP_DOMAIN),
                'arguments'    => [
                    'supports'             => [
                        'title',
                        'comments',
                        'thumbnail',
                        'publicize',
                        'cover-images',
                    ],
                    'register_meta_box_cb' => [$this, 'meta_box_cb'],
                    //phpcs:disable
                    'menu_icon'            => 'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMTYgMjE2Ij48dGl0bGU+bWFuZ2EtcHJlc3MtbWVudS1pY29uPC90aXRsZT48cGF0aCBmaWxsPSJibGFjayIgZD0iTTEwOCwwYTE0OS44MSwxNDkuODEsMCwwLDEsNDIsNS43LDEzMC4yNSwxMzAuMjUsMCwwLDEsMzQuMzgsMTUuMThxMTQuNzYsOS41LDIzLjIsMjIuMzZhNDkuNDYsNDkuNDYsMCwwLDEsMCw1NS4yN3EtOC40MywxMi44Ny0yMy4yLDIyLjU3QTEyMi4wNywxMjIuMDcsMCwwLDEsMTUwLDEzNi4yN2ExNTUuNTcsMTU1LjU3LDAsMCwxLTQyLDUuNDhIOTguNzJRODcuMzIsMTY1LDc1LjMsMTc4Ljg4VDQ0LjUxLDIwNC40UTI1LjczLDIxNiwwLDIxNmE3My40MSw3My40MSwwLDAsMCwzMC4zOC0xOS42MkExNDQuNDUsMTQ0LjQ1LDAsMCwwLDUwLjIsMTY5LjU5cTcuNjEtMTMuNSwxMy41LTMzLjc1QTEzMS42NSwxMzEuNjUsMCwwLDEsMzgsMTI1LjA5YTkwLjA4LDkwLjA4LDAsMCwxLTIwLjI1LTE1QTY0LjY1LDY0LjY1LDAsMCwxLDQuNjQsOTEuNzYsNDguNzMsNDguNzMsMCwwLDEsMCw3MC44OCw0OS40NSw0OS40NSwwLDAsMSw4LjQ0LDQzLjI0cTguNDMtMTIuODcsMjMuMi0yMi4zNkExMzAuMjUsMTMwLjI1LDAsMCwxLDY2LDUuNywxNDkuODEsMTQ5LjgxLDAsMCwxLDEwOCwwWm01MC4yLDQ3LjY3YTYuNTQsNi41NCwwLDAsMC0uNDItNCwxNC42NSwxNC42NSwwLDAsMC0yLjUzLTMuNzksMTIuNzEsMTIuNzEsMCwwLDAtMy44LTMsOS4zNyw5LjM3LDAsMCwwLTQuMjItMSw3LjM5LDcuMzksMCwwLDAtMi45NSwxLjI2Yy0xLjQuODUtMy4yMywyLjExLTUuNDgsMy44UzEzNCw0NC43MiwxMzEsNDcuMjVsLTEwLjMzLDguODZjLjI4LTEuMTMuNTYtMi4zOS44NC0zLjhhMzQsMzQsMCwwLDEsMS00bDEuMjctMy43OWEzNS41MywzNS41MywwLDAsMCwxLjA2LTRjLjI4LTEuNjkuNTYtMy4yMy44NC00LjY0cy41Ni0yLjc0Ljg0LTRhMTYuNTcsMTYuNTcsMCwwLDAsLjQyLTMuNTh2LTNhNyw3LDAsMCwwLS4yMS0xLjY5LDIsMiwwLDAsMC0uODQtMS4yNiw0LjY3LDQuNjcsMCwwLDAtMS42OS0uNjMsMTIuOTIsMTIuOTIsMCwwLDAtNC42NCwwbC0yLjExLjQyYTIwLjA2LDIwLjA2LDAsMCwwLTIuMzIuNjMsMTAuMzQsMTAuMzQsMCwwLDAtMi41MywxLjI3LDE5LjM2LDE5LjM2LDAsMCwwLTIuNTMsMS4wNUE4LjA4LDguMDgsMCwwLDAsMTA4LDI2LjU4YTExLDExLDAsMCwwLTEuMjcsMS40NywxNSwxNSwwLDAsMC0uODQsMS40OHEtLjQyLDMuOC0uODQsN2MtLjI4LDIuMTEtLjUsNC4wOC0uNjQsNS45MXMtLjIxLDMuNTEtLjIxLDUuMDZ2NHEtNS4wNi0zLjgtOS4wNy02LjU0dC02LjU0LTQuNjRhMjguNjIsMjguNjIsMCwwLDAtNC4yMS0yLjc0LDcuMzMsNy4zMywwLDAsMC0yLjExLS44NSwzLjc0LDMuNzQsMCwwLDAtMS42OS40MmMtLjU2LjI5LTEuMi42NC0xLjksMS4wNmE5LjUsOS41LDAsMCwwLTEuOSwxLjQ4LDkuOTMsOS45MywwLDAsMC0xLjY5LDIuNTNjLS41Ni44NC0xLjEyLDEuNjEtMS42OCwyLjMyYTcuMiw3LjIsMCwwLDAtMS4yNywyLjMyYy0uMjguODQtLjU2LDEuNjItLjg0LDIuMzJzLS41NywxLjMzLS44NSwxLjlBMy4zOCwzLjM4LDAsMCwwLDcwLDUyLjUydjEuMjdhMi4zNCwyLjM0LDAsMCwwLC4yMSwxLjA1LDEuOSwxLjksMCwwLDEsLjIxLjg1Yy41Ny41NiwxLjIsMS4xMiwxLjksMS42OWExNS42MiwxNS42MiwwLDAsMCwyLjc0LDEuNjhjMS4xMy41NiwyLjM5LDEuMjcsMy44LDIuMTFzMywxLjY5LDQuNjQsMi41M0w5OC4zLDcxLjMsNzguNDcsODEuNDJjLTIuNTMsMS40MS00Ljc4LDIuNzQtNi43NSw0cy0zLjY2LDIuMzItNS4wNiwzLjE2YTE3LjM4LDE3LjM4LDAsMCwwLTMuMTcsMi4zMkE3Ljk0LDcuOTQsMCwwLDAsNjIsOTIuODFhNi4xOSw2LjE5LDAsMCwwLC4yMSwzLjU5QTEwLjM0LDEwLjM0LDAsMCwwLDY0LjM0LDEwMGE5LjU0LDkuNTQsMCwwLDAsNy4zOCwzLjM4LDE1LjQyLDE1LjQyLDAsMCwwLDIuNTMtLjIxLDguNjgsOC42OCwwLDAsMCwyLjUzLS44NSwzMS4xLDMxLjEsMCwwLDEsMy0xLjI2LDEzLjc0LDEzLjc0LDAsMCwwLDMuMzgtMS45TDkzLjY2LDkyYy0xLjEzLDMuMDktMi4xOCw1LjkxLTMuMTcsOC40NHMtMS43Niw0LjcxLTIuMzIsNi41NC0xLDMuMy0xLjQ3LDQuNDNhOC43Miw4LjcyLDAsMCwwLS42NCwyLjEsNC41Myw0LjUzLDAsMCwwLS4yMSwyLjMyLDMuMTQsMy4xNCwwLDAsMCwyLjMyLDIuNTQsMTEuMywxMS4zLDAsMCwwLDIuNTMuMjFBMTcuNzIsMTcuNzIsMCwwLDAsOTYsMTE3LjdhMjAuNjEsMjAuNjEsMCwwLDAsNS4wNi0yLjMyLDE2LjM1LDE2LjM1LDAsMCwwLDQtMy41OCwxMCwxMCwwLDAsMCwyLjExLTQuNjRsNi4zMi0yMi4zNiw3LjYsNy41OXEzLjM4LDMuMzgsNS40OCw1LjdhMzcuMTksMzcuMTksMCwwLDAsMy4zOCwzLjM3Yy44NC43LDEuNCwxLjA2LDEuNjgsMS4wNmE2LjQ4LDYuNDgsMCwwLDAsMS42OS0uMjIsNC42OSw0LjY5LDAsMCwwLDEuNjktLjg0LDE1LjgzLDE1LjgzLDAsMCwwLDEuNjktMS40OGwxLjY5LTEuNjhjLjU2LS44NSwxLjA1LTEuNTUsMS40Ny0yLjExYTksOSwwLDAsMCwxLjA2LTEuOWMuMjgtLjcuNTYtMS4zNC44NC0xLjlsLjg0LTEuNjl2LS42M2ExLjYxLDEuNjEsMCwwLDEsLjIxLS42MywxLjUsMS41LDAsMCwwLC4yMi0uNjR2LS42M2E5LjcxLDkuNzEsMCwwLDAtLjQzLTIuNzQsMTUuOSwxNS45LDAsMCwwLTEuMDUtMi43NCwxMywxMywwLDAsMC0xLjY5LTIuNTMsMTYuMjksMTYuMjksMCwwLDAtMi43NC0yLjU0TDEyNy40MSw3MGwxMy4yOS04Ljg2cTUuNjktMy44LDkuNDktNi41NGMyLjUzLTEuODMsNC40My0zLjMsNS42OS00LjQzQTEwLjYzLDEwLjYzLDAsMCwwLDE1OC4yLDQ3LjY3WiIvPjwvc3ZnPg==',
                    //phpcs:enable
                    'rewrite'              => [
                        'slug' => $this->get_front_slug(),
                    ],
                    'has_archive'          => $this->get_comic_archives_slug(),
                    'taxonomies'           => [
                        $taxonomy->get_name(),
                    ],
                ],
            ]
        );
    }

    /**
     * Get current user-specified front-slug for Comics
     *
     * @return string
     */
    public function get_front_slug()
    {
        /**
         * mangapress_comic_front_slug
         * Allow plugins (or options) to modify post-type front slug
         *
         * @param string $slug Default post-type slug
         * @return string
         * @since 4.0.0
         */
        return apply_filters('mangapress_comic_front_slug', 'comics/' . $this->slug);
    }

    /**
     * Get current user-specified front-slug for Comic archives
     *
     * @return string
     */
    public function get_comic_archives_slug()
    {
        /**
         * mangapress_comic_archives_slug
         * Allow plugins (or options) to modify post-type archive slug
         *
         * @param string $slug Default post-type archive slug
         * @return string
         */
        return apply_filters('mangapress_comic_archives_slug', $this->archive_slug);
    }

    /**
     * Add new rewrite rules for Comic post-type
     */
    public function rewrite_rules()
    {
        $post_type = self::POST_TYPE;
        $slug      = $this->get_front_slug();

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]&post_type=' . $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]&post_type=' . $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]&post_type=' . $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&post_type=' . $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]&post_type=' . $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]&post_type=' . $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]&post_type=' . $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&post_type=' . $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$",
            'index.php?year=$matches[1]&feed=$matches[2]&post_type=' . $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$",
            'index.php?year=$matches[1]&feed=$matches[2]&post_type=' . $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/page/?([0-9]{1,})/?$",
            'index.php?year=$matches[1]&paged=$matches[2]&post_type=' . $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/?$",
            'index.php?year=$matches[1]&post_type=' . $post_type,
            'top'
        );
    }

    /**
     * Set the comic archive slug
     *
     * @param string $slug
     *
     * @return string
     */
    public function set_comic_archives_slug($slug)
    {
        $comic_archive_slug = 'comic-archives';
        if (!$comic_archive_slug) {
            return $slug;
        }

        return $comic_archive_slug;
    }

    /**
     * Modify header columns for Comic Post-type
     *
     * @param array $column
     * @return void
     * @global \WP_Post $post
     */
    public function comics_headers($column)
    {
        global $post;

        if ("cb" == $column) {
            echo "<input type=\"checkbox\" value=\"{$post->ID}\" name=\"post[]\" />";
        } elseif ("thumbnail" == $column) {
            $thumbnail_html = get_the_post_thumbnail($post->ID, 'thumbnail', ['class' => 'wp-caption']);

            if ($thumbnail_html) {
                $edit_link = get_edit_post_link($post->ID, 'display');
                echo "<a href=\"{$edit_link}\">{$thumbnail_html}</a>";
            } else {
                echo "No image";
            }
        } elseif ("title" == $column) {
            echo $post->post_title;
        } elseif ("series" == $column) {
            $series = wp_get_object_terms($post->ID, 'mangapress_series');
            if (!empty($series)) {
                $series_html = [];
                /**
                 * @var \WP_Term $s
                 */
                foreach ($series as $s) {
                    array_push(
                        $series_html,
                        vsprintf(
                            '<a href="%s">%s</a>',
                            [
                                get_term_link($s->slug, 'mangapress_series'),
                                $s->name,
                            ]
                        )
                    );
                }

                echo implode($series_html, ", ");
            }
        } elseif ("post_date" == $column) {
            echo date("Y/m/d", strtotime($post->post_date));
        } elseif ("description" == $column) {
            echo $post->post_excerpt;
        } elseif ("author" == $column) {
            echo $post->post_author;
        }
    }


    /**
     * Modify comic columns for Comics screen
     *
     * @param array $columns
     * @return array
     */
    public function comics_columns($columns)
    {

        $columns = [
            'cb'          => '<input type="checkbox" />',
            'thumbnail'   => __('Thumbnail', MP_DOMAIN),
            'title'       => __('Comic Title', MP_DOMAIN),
            'series'      => __('Series', MP_DOMAIN),
            'description' => __('Description', MP_DOMAIN),
            'author'      => __('Author', MP_DOMAIN),
        ];

        return $columns;
    }

    /**
     * Meta box call-back function.
     *
     * @return void
     */
    public function meta_box_cb()
    {
        if (mangapress_theme_supports_cover_images()) {
            add_meta_box(
                'cover-image',
                __('Cover Image', MP_DOMAIN),
                [$this, 'cover_image_cb'],
                $this->post_type->get_name(),
                'normal',
                'high'
            );
        }

        add_meta_box(
            'comic-image',
            __('Comic Image', MP_DOMAIN),
            [$this, 'comic_meta_box_cb'],
            $this->post_type->get_name(),
            'normal',
            'high'
        );

        /*
         * Because we don't need this...the comic image is the "Featured Image"
         * TODO add an option for users to override this "functionality"
         */
        remove_meta_box('postimagediv', 'mangapress_comic', 'side');
    }


    /**
     * Comic meta box
     */
    public function comic_meta_box_cb()
    {
        include_once MP_ABSPATH . 'resources/admin/pages/meta-box-add-comic.php';
    }

    /**
     * Cover image meta box
     */
    public function cover_image_cb()
    {
        include_once MP_ABSPATH . 'resources/admin/pages/meta-box-add-cover.php';
    }

    /**
     * Enqueue scripts for post-edit and post-add screens
     *
     * @return void
     * @global \WP_Post $post
     */
    public function enqueue_scripts()
    {
        $current_screen = get_current_screen();

        if (!isset($current_screen->post_type) || !isset($current_screen->base)) {
            return;
        }

        if (!($current_screen->post_type == self::POST_TYPE && $current_screen->base == 'post')) {
            return;
        }

        // Include in admin_enqueue_scripts action hook
        wp_enqueue_media();
        wp_register_script(
            'mangapress-media-popup',
            MP_URLPATH . '/resources/assets/js/add-comic.js',
            ['jquery'],
            MP_VERSION,
            true
        );

        wp_localize_script(
            'mangapress-media-popup',
            strtoupper(MP_DOMAIN),
            [
                'title'  => __('Upload or Choose Your Comic Image File', MP_DOMAIN),
                'button' => __('Insert Comic into Post', MP_DOMAIN),
            ]
        );

        wp_enqueue_script('mangapress-media-popup');
    }

    /**
     * Retrieve image HTML
     *
     * @return void
     */
    public function get_image_html_ajax()
    {
        // nonce verification

        // get image
        $image_ID = filter_input(INPUT_POST, 'id') ? filter_input(INPUT_POST, 'id') : false;
        $action   = filter_input(INPUT_POST, 'action')
            ? filter_input(INPUT_POST, 'action') : Actions::ACTION_INSERT_COMIC;

        header("Content-type: application/json");
        $is_cover = (($action === Actions::ACTION_INSERT_COVER) || ($action === Actions::ACTION_REMOVE_COVER));
        if ($action == Actions::ACTION_INSERT_COMIC || $action == Actions::ACTION_INSERT_COVER) {
            if ($image_ID) {
                echo json_encode(['html' => $this->get_image_html($image_ID, $is_cover),]);
            }
        } else {
            echo json_encode(['html' => $this->get_remove_image_html($is_cover),]);
        }

        die();
    }


    /**
     * Retrieve image html
     *
     * @param int $image_ID
     * @param boolean $is_cover
     * @return string
     */
    public function get_image_html($image_ID, $is_cover = false)
    {
        $image_html = wp_get_attachment_image($image_ID, 'medium');
        if ($image_html == '') {
            return '';
        }

        ob_start();
        include MP_ABSPATH . 'resources/admin/pages/set-image-link.php';
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }


    /**
     * Reset comic image html
     * @param boolean $is_cover
     * @return string
     */
    public function get_remove_image_html($is_cover = false)
    {
        ob_start();
        include MP_ABSPATH . 'resources/admin/pages/remove-image-link.php';
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }


    /**
     * Save post meta data. By default, Manga+Press uses the _thumbnail_id
     * meta key. This is the same meta key used for the post featured image.
     *
     * @param int $post_id
     * @param \WP_Post $post
     *
     * @return int
     */
    public function save_post($post_id, $post)
    {
        if ($post->post_type !== self::POST_TYPE || empty($_POST)) {
            return $post_id;
        }

        if (wp_verify_nonce(filter_input(INPUT_POST, '_insert_comic'), Actions::NONCE_INSERT_COMIC)) {
            $image_ID = (int)filter_input(INPUT_POST, '_mangapress_comic_image', FILTER_SANITIZE_NUMBER_INT);
            if ($image_ID) {
                set_post_thumbnail($post_id, $image_ID);
            }
        }

        if (wp_verify_nonce(filter_input(INPUT_POST, '_insert_cover-image'), Actions::NONCE_INSERT_COMIC)) {
            $cover_image_id = (int)filter_input(INPUT_POST, '_mangapress_cover_image', FILTER_SANITIZE_NUMBER_INT);
            if ($cover_image_id) {
                update_post_meta($post_id, 'mangapress_cover_image_id', $cover_image_id);
            } else {
                delete_post_meta($post_id, 'mangapress_cover_image_id');
            }
        }

        // if no terms have been assigned, assign the default
        if (!isset($_POST['tax_input'][self::TAX_SERIES][0])
            || ($_POST['tax_input'][self::TAX_SERIES][0] == 0
                && count($_POST['tax_input'][self::TAX_SERIES]) == 1)) {
            $default_cat = get_option('mangapress_default_category');
            wp_set_post_terms($post_id, $default_cat, self::TAX_SERIES);
        } else {
            // continue as normal
            wp_set_post_terms($post_id, $_POST['tax_input'][self::TAX_SERIES], self::TAX_SERIES);
        }

        return $post_id;
    }
}
