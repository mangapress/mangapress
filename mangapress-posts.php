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
     * Class for initializing custom post-type
     *
     * @var MangaPress_PostType
     */
    private $_post_type = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_register_post_type();

        // Setup Manga+Press Post Options box
        add_action("wp_ajax_" . self::ACTION_GET_IMAGE_HTML, array($this, 'get_image_html_ajax'));
        add_action("wp_ajax_" . self::ACTION_REMOVE_IMAGE, array($this, 'get_image_html_ajax'));
        add_action('save_post', array($this, 'save_post'), 500, 2);
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
            'name' => 'mangapress_series',
            'singlename' => 'Series',
            'pluralname' => 'Series',
            'objects' => array('mangapress_comic'),
            'arguments' => array(
                'hierarchical' => true,
                'query_var' => 'series',
                'rewrite' => array('slug' => 'series'),
            ),            
        ));
        
        $this->_post_type = new MangaPress_PostType(array(
            'name'          => self::POST_TYPE,
            'pluralname'    => 'Comics',
            'singlename' => 'Comic',
            'arguments'     => array(
                'supports'      => array(
                    'title',
                    'comments',
                    'thumbnails',
                ),
                'register_meta_box_cb' => array($this, 'meta_box_cb'),
                'menu_icon' => MP_URLPATH . 'images/menu_icon.png',
                'rewrite'   => array(
                    'slug' => 'comic',
                ),
                'taxonomies' => array(
                    $taxonomy->get_name(),
                ),
            ),
        ));
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
            plugins_url( '/js/add-comic.js', __FILE__ ),
            array( 'jquery' ),
            MP_VERSION,
            true
        );

        wp_localize_script(
            'mangapress-media-popup',
            'MANGAPRESS',
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
                echo $thumbnail_html;
            } else {
                echo "No image";
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
        $image_ID = isset($_POST['id']) ? absint($_POST['id']) : false;
        if ($image_ID) {
            $action = isset($_POST['action']) ? $_POST['action'] : self::ACTION_REMOVE_IMAGE;
            
            header("Content-type: application/json");
            if ($action == self::ACTION_GET_IMAGE_HTML){
                echo json_encode(array('html' => $this->get_image_html($image_ID),));
            } else {
                echo json_encode(array('html' => $this->get_remove_image_html(),));
            }
        }
                                
        die();
    }
    
    /**
     * Retrieve image html
     * 
     * @param integer $post_id
     * @return string
     */
    public function get_image_html($image_ID)
    {
        $image_html = wp_get_attachment_image($image_ID, 'medium');
        if ($image_html == '')
            return '';
        
        $image_html .= "<p class=\"hide-if-no-js\"><a href=\"#\" data-action=\""
                    . self::ACTION_REMOVE_IMAGE . "\" data-nonce=\""
                    . wp_create_nonce(self::NONCE_INSERT_COMIC) . "\" id=\"js-remove-comic-thumbnail\">"
                    . esc_html__( 'Remove Comic image', MP_DOMAIN )
                    . "</a></p>";
        
        return $image_html;
    }
    
    /**
     * Reset comic image html
     * 
     * @return string
     */
    public function get_remove_image_html()
    {
        $html = "<a id=\"choose-from-library-link\" href=\"#\" data-nonce=\""
                . wp_create_nonce(self::NONCE_INSERT_COMIC) . "\" data-action=\"" . esc_attr(self::ACTION_GET_IMAGE_HTML) . "\""
                . " data-choose=\"" .  esc_attr__('Choose a Comic Image', MP_DOMAIN) . "\""
                . " data-update=\"" . esc_attr__('Set as Comic Image', MP_DOMAIN) . "\">"
                . __( 'Set Comic Image', MP_DOMAIN)
                . "</a>";
        
        return $html;
    }

    /**
     * Save post meta data
     * 
     * @param int $post_id
     * @param WP_Post $post
     * 
     * @return void
     */
    public function save_post($post_id, $post)
    {
        if ($post->post_type !== self::POST_TYPE)
            return $post_id;
        
        do_action('do_save_mangapress_comic', $post_id, $post);
        
        
    }
    
}
