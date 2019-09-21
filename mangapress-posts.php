<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author  Jessica Green <support@manga-press.com>
 */

namespace MangaPress;

use MangaPress\Lib\Taxonomy as Taxonomy;
use MangaPress\Lib\PostType as PostType;

/**
 * MangaPress Posts class
 * Handles functionality for the Comic post-type
 *
 * @subpackage Posts
 * @author     Jessica Green <support@manga-press.com>
 */
class Posts
{
    /**
     * Get image html
     *
     * @var string
     */
    const ACTION_GET_IMAGE_HTML = 'mangapress-get-image-html';


    /**
     * Remove image html and return Add Image string
     *
     * @var string
     */
    const ACTION_REMOVE_IMAGE = 'mangapress-remove-image';


    /**
     * Nonce string
     *
     * @var string
     */
    const NONCE_INSERT_COMIC = 'mangapress_comic-insert-comic';


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
     * Class for initializing custom post-type
     *
     * @var Posts
     */
    private $post_type = null;


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
     * Constructor
     */
    public function __construct()
    {
        $this->register_post_type();
        $this->rewrite_rules();

        // Setup Manga+Press Post Options box
        add_action("wp_ajax_" . self::ACTION_GET_IMAGE_HTML, [$this, 'get_image_html_ajax']);
        add_action("wp_ajax_" . self::ACTION_REMOVE_IMAGE, [$this, 'get_image_html_ajax']);
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
    private function register_post_type()
    {
        // register taxonomy
        $taxonomy = new Taxonomy(
            [
                'name'       => self::TAX_SERIES,
                'textdomain' => MP_DOMAIN,
                'singlename' => __('Series', MP_DOMAIN),
                'pluralname' => __('Series', MP_DOMAIN),
                'objects'    => ['mangapress_comic'],
                'arguments'  => [
                    'hierarchical' => true,
                    'query_var'    => 'series',
                    'rewrite'      => [
                        'slug' => 'series',
                    ],
                ],
            ]
        );

        $this->post_type = new PostType(
            [
                'name'       => self::POST_TYPE,
                'textdomain' => MP_DOMAIN,
                'pluralname' => __('Comics', MP_DOMAIN),
                'singlename' => __('Comic', MP_DOMAIN),
                'arguments'  => [
                    'supports'             => [
                        'title',
                        'comments',
                        'thumbnails',
                        'publicize',
                    ],
                    'register_meta_box_cb' => [$this, 'meta_box_cb'],
                    'menu_icon'            => null,
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
     * Add new rewrite rules for Comic post-type
     */
    private function rewrite_rules()
    {
        if (Bootstrap::get_option('basic', 'latestcomic_page') == '') {
            add_rewrite_endpoint($this->get_latest_comic_slug(), EP_ROOT);
        }
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
         */
        return apply_filters('mangapress_comic_front_slug', $this->slug);
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
     * Set the comic archive slug
     *
     * @param string $slug
     *
     * @return string
     */
    public static function set_comic_archives_slug($slug)
    {
        $comic_archive_slug = Bootstrap::get_option('basic', 'comicarchive_page');
        if (!$comic_archive_slug) {
            return $slug;
        }

        return $comic_archive_slug;
    }

    /**
     * Get current user-specified front-slug for Latest Comic
     *
     * @return string
     */
    public function get_latest_comic_slug()
    {
        /**
         * mangapress_latest_comic_slug
         * Allow plugins (or options) to override the Latest Comic slug
         *
         * @param string $slug Default Latest Comic slug
         * @return string
         */
        return apply_filters('mangapress_latest_comic_slug', $this->latest_comic_slug);
    }


    /**
     * Set the comic slug to a specified page
     *
     * @param string $slug
     *
     * @return string mixed
     */
    public static function set_latest_comic_slug($slug)
    {
        $latest_comic_slug = Bootstrap::get_option('basic', 'latestcomic_page');
        if (!$latest_comic_slug) {
            return $slug;
        }

        return $latest_comic_slug;
    }


    /**
     * Meta box call-back function.
     *
     * @return void
     */
    public function meta_box_cb()
    {
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
     *
     * @return void
     */
    public function comic_meta_box_cb()
    {
        include_once MP_ABSPATH . 'includes/pages/meta-box-add-comic.php';
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
            plugins_url('/assets/js/add-comic.js', __FILE__),
            ['jquery'],
            MP_VERSION,
            true
        );

        wp_localize_script(
            'mangapress-media-popup',
            MP_DOMAIN,
            [
                'title'  => __('Upload or Choose Your Comic Image File', MP_DOMAIN),
                'button' => __('Insert Comic into Post', MP_DOMAIN),
            ]
        );

        wp_enqueue_script('mangapress-media-popup');
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
            $thumbnail_html = get_the_post_thumbnail($post->ID, 'comic-admin-thumb', ['class' => 'wp-caption']);

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
            "cb"          => "<input type=\"checkbox\" />",
            "thumbnail"   => __("Thumbnail", MP_DOMAIN),
            "title"       => __("Comic Title", MP_DOMAIN),
            "series"      => __("Series", MP_DOMAIN),
            "description" => __("Description", MP_DOMAIN),
        ];

        return $columns;
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
            ? filter_input(INPUT_POST, 'action') : self::ACTION_REMOVE_IMAGE;

        header("Content-type: application/json");
        if ($action == self::ACTION_GET_IMAGE_HTML) {
            if ($image_ID) {
                echo json_encode(['html' => $this->get_image_html($image_ID),]);
            }
        } else {
            echo json_encode(['html' => $this->get_remove_image_html(),]);
        }

        die();
    }


    /**
     * Retrieve image html
     *
     * @param int $image_ID
     * @return string
     */
    public function get_image_html($image_ID)
    {
        $image_html = wp_get_attachment_image($image_ID, 'medium');
        if ($image_html == '') {
            return '';
        }

        ob_start();
        include_once MP_ABSPATH . 'includes/pages/set-image-link.php';
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }


    /**
     * Reset comic image html
     *
     * @return string
     */
    public function get_remove_image_html()
    {

        ob_start();
        include_once MP_ABSPATH . 'includes/pages/remove-image-link.php';
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

        if (!wp_verify_nonce(filter_input(INPUT_POST, '_insert_comic'), self::NONCE_INSERT_COMIC)) {
            return $post_id;
        }

        $image_ID = (int)filter_input(INPUT_POST, '_mangapress_comic_image', FILTER_SANITIZE_NUMBER_INT);
        if ($image_ID) {
            set_post_thumbnail($post_id, $image_ID);
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
