<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */

/**
 * MangaPress Posts class
 * Handles functionality for the Comic post-type
 *
 * @package MangaPress
 * @subpackage MangaPress_Posts
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */

class MangaPress_Posts
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
    const ACTION_REMOVE_IMAGE  = 'mangapress-remove-image';


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
     * @var MangaPress_PostType
     */
    private $_post_type = null;


    /**
     * Post-type Slug. Defaults to comic.
     * @var string
     */
    protected $slug = 'comic';


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_register_post_type();
        $this->_rewrite_rules();

        // Setup Manga+Press Post Options box
        add_action("wp_ajax_" . self::ACTION_GET_IMAGE_HTML, array($this, 'get_image_html_ajax'));
        add_action("wp_ajax_" . self::ACTION_REMOVE_IMAGE, array($this, 'get_image_html_ajax'));
        add_action('save_post_mangapress_comic', array($this, 'save_post'), 500, 2);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

        /*
         * Actions and filters for modifying our Edit Comics page.
         */
        add_action('manage_posts_custom_column', array($this, 'comics_headers'));
        add_filter('manage_edit-mangapress_comic_columns', array($this, 'comics_columns'));

    }


    /**
     * Register the post-type
     *
     * @return void
     */
    private function _register_post_type()
    {
        // register taxonomy
        $taxonomy = new MangaPress_Taxonomy(array(
            'name'       => self::TAX_SERIES,
            'textdomain' => MP_DOMAIN,
            'singlename' => __('Series', MP_DOMAIN),
            'pluralname' => __('Series', MP_DOMAIN),
            'objects'    => array('mangapress_comic'),
            'arguments'  => array(
                'hierarchical' => true,
                'query_var'    => 'series',
                'rewrite'      => array(
                    'slug' => 'series'
                ),
            ),
        ));

        $this->_post_type = new MangaPress_PostType(array(
            'name'          => self::POST_TYPE,
            'textdomain'    => MP_DOMAIN,
            'pluralname'    => __('Comics', MP_DOMAIN),
            'singlename'    => __('Comic', MP_DOMAIN),
            'arguments'     => array(
                'supports'      => array(
                    'title',
                    'comments',
                    'thumbnails',
                    'publicize',
                ),
                'register_meta_box_cb' => array($this, 'meta_box_cb'),
                'menu_icon' => null,
                'rewrite'   => array(
                    'slug' => $this->get_slug(),
                ),
                'taxonomies' => array(
                    $taxonomy->get_name(),
                ),
            ),
        ));

    }


    /**
     * Add new rewrite rules for Comic post-type
     */
    private function _rewrite_rules()
    {
        $post_type = self::POST_TYPE;
        $slug      = $this->get_slug();

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]&post_type=' .  $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]&post_type=' .  $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]&post_type=' .  $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&post_type=' .  $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]&post_type=' .  $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]&post_type=' .  $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]&post_type=' .  $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/([0-9]{1,2})/?$",
            'index.php?year=$matches[1]&monthnum=$matches[2]&post_type=' .  $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$",
            'index.php?year=$matches[1]&feed=$matches[2]&post_type=' .  $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$",
            'index.php?year=$matches[1]&feed=$matches[2]&post_type=' .  $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/page/?([0-9]{1,})/?$",
            'index.php?year=$matches[1]&paged=$matches[2]&post_type=' .  $post_type,
            'top'
        );

        add_rewrite_rule(
            "{$slug}/([0-9]{4})/?$",
            'index.php?year=$matches[1]&post_type=' .  $post_type,
            'top'
        );
    }


    /**
     * Get current user-specified front-slug for Comic archives
     *
     * @return string
     */
    public function get_slug()
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
     * Meta box call-back function.
     *
     * @return void
     */
    public function meta_box_cb()
    {
        add_meta_box(
            'comic-image',
            __('Comic Image', MP_DOMAIN),
            array($this, 'comic_meta_box_cb'),
            $this->_post_type->get_name(),
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
        require_once MP_ABSPATH . 'includes/pages/meta-box-add-comic.php';
    }


    /**
     * Enqueue scripts for post-edit and post-add screens
     *
     * @global WP_Post $post
     * @return void
     */
    public function enqueue_scripts()
    {
        $current_screen = get_current_screen();

        if (!isset($current_screen->post_type) || !isset($current_screen->base))
            return;

        if (!($current_screen->post_type == self::POST_TYPE && $current_screen->base == 'post'))
            return;

        // Include in admin_enqueue_scripts action hook
        wp_enqueue_media();
        wp_register_script(
            'mangapress-media-popup',
            plugins_url( '/assets/js/add-comic.js', __FILE__ ),
            array( 'jquery' ),
            MP_VERSION,
            true
        );

        wp_localize_script(
            'mangapress-media-popup',
            MP_DOMAIN,
            array(
                'title'  => __('Upload or Choose Your Comic Image File', MP_DOMAIN),
                'button' => __('Insert Comic into Post', MP_DOMAIN),
            )
        );

        wp_enqueue_script('mangapress-media-popup');
    }


    /**
     * Modify header columns for Comic Post-type
     *
     * @global WP_Post $post
     * @param array $column
     * @return void
     */
    public function comics_headers($column)
    {
        global $post;

        if ("cb" == $column) {
            echo "<input type=\"checkbox\" value=\"{$post->ID}\" name=\"post[]\" />";
        } elseif ("thumbnail" == $column) {

            $thumbnail_html = get_the_post_thumbnail($post->ID, 'comic-admin-thumb', array('class' => 'wp-caption'));

            if ($thumbnail_html) {
                $edit_link = get_edit_post_link($post->ID, 'display');
                echo "<a href=\"{$edit_link}\">{$thumbnail_html}</a>";
            } else {
                echo "<p class='error' style='border-left: 5px solid #dd3d36; padding-left: 5px; '>No image</p>";
            }
        } elseif ("title" == $column) {
            echo $post->post_title;
        } elseif ("series" == $column) {
            $series = wp_get_object_terms( $post->ID, 'mangapress_series' );
            if (!empty($series)){
                $series_html = array();
                foreach ($series as $s)
                    array_push($series_html, '<a href="' . get_term_link($s->slug, 'mangapress_series') . '">'.$s->name."</a>");

                echo implode($series_html, ", ");
            }
        } elseif ("post_date" == $column) {
            echo date( "Y/m/d", strtotime($post->post_date) );

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

        $columns = array(
            "cb"          => "<input type=\"checkbox\" />",
            "thumbnail"   => __("Thumbnail", MP_DOMAIN),
            "title"       => __("Comic Title", MP_DOMAIN),
            "series"      => __("Series", MP_DOMAIN),
            "description" => __("Description", MP_DOMAIN),
        );

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
        if ($action == self::ACTION_GET_IMAGE_HTML){
            if ($image_ID) {
                echo json_encode(array('html' => $this->get_image_html($image_ID),));
            }
        } else {
            echo json_encode(array('html' => $this->get_remove_image_html(),));
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
        if ($image_html == '')
            return '';


        /**
         * Allows plugins to override default markup returned by Set Image
         * @param string $image_html Image markup from wp_get_attachment_image
         * @return string
         */
        $html = apply_filters('mangapress_set_image_html_custom', $image_html);
        if ($html) {
            return $html;
        }

        ob_start();
        require_once MP_ABSPATH . 'includes/pages/set-image-link.php';
        $html = ob_get_contents();
        ob_end_clean();

        /**
         * Allows plugins to modify default markup returned by Set Image
         * @param string $html Markup generated by set-image-link.php
         * @return string
         */
        return apply_filters('mangapress_set_image_html', $html);
    }


    /**
     * Reset comic image html
     *
     * @return string
     */
    public function get_remove_image_html()
    {
        /**
         * Allow plugins to override the "Remove Image" markup
         * @param string null
         * @return string
         */
        $html = apply_filters('mangapress_remove_image_html_custom', '');
        if ($html) {
            return $html;
        }

        ob_start();
        require_once MP_ABSPATH . 'includes/pages/remove-image-link.php';
        $html = ob_get_contents();
        ob_end_clean();

        /**
         * Allow plugins to modify the default "Remove Image" markup
         * @param string $html Markup returned from remove-image-link.php
         * @return string
         */
        return apply_filters('mangapress_remove_image_html', $html);
    }


    /**
     * Save post meta data. By default, Manga+Press uses the _thumbnail_id
     * meta key. This is the same meta key used for the post featured image.
     *
     * @param int $post_id
     * @param WP_Post $post
     *
     * @return int
     */
    public function save_post($post_id, $post)
    {
        $flash_messages = MangaPress_Bootstrap::get_instance()->get_helper('flashmessage');

        if ($post->post_type !== self::POST_TYPE || empty($_POST))
            return $post_id;

        if (!wp_verify_nonce(filter_input(INPUT_POST, '_insert_comic'), self::NONCE_INSERT_COMIC))
            return $post_id;

        $image_ID = (int)filter_input(INPUT_POST, '_mangapress_comic_image', FILTER_SANITIZE_NUMBER_INT);
        if ($image_ID) {
            set_post_thumbnail($post_id, $image_ID);
        } else {
            $flash_messages->queue_flash_message('error', array(
                'id' => $post_id,
                'message' => 'No comic has been set.',
            ));
        }

        // if no terms have been assigned, assign the default
        if (!isset($_POST['tax_input'][self::TAX_SERIES][0]) || ($_POST['tax_input'][self::TAX_SERIES][0] == 0 && count($_POST['tax_input'][self::TAX_SERIES]) == 1)) {
            $default_cat = get_option('mangapress_default_category');
            wp_set_post_terms($post_id, $default_cat, self::TAX_SERIES);
        } else {
            // continue as normal
            wp_set_post_terms($post_id, $_POST['tax_input'][self::TAX_SERIES], self::TAX_SERIES);
        }

        return $post_id;

    }

}
