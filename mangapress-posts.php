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
     * Ajax action hook for adding comic images
     *
     * @var string
     */
    private $_ajax_action_add_comic    = 'add-comic';

    /**
     * Ajax action hook for removing comic images
     *
     * @var string
     */
    private $_ajax_action_remove_comic = 'remove-comic';

    /**
     * Nonce string
     *
     * @var string
     */
    private $_nonce_insert_comic       = 'mangapress_comic-insert-comic';

    /**
     * Class for initializing custom post-type
     *
     * @var ComicPostType
     */
    private $_post_type = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_register_post_type();

        // Setup Manga+Press Post Options box
        add_action("wp_ajax_{$this->_ajax_action_add_comic}", array($this, 'wp_ajax_comic_handler'));
        add_action("wp_ajax_{$this->_ajax_action_remove_comic}", array($this, 'wp_ajax_comic_handler'));

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
            'name'          => 'mangapress_comic',
            'pluralname'    => 'Comics',
            'singlename' => 'Comic',
            'arguments'     => array(
                'supports'      => array(
                    'title',
                    'comments',
                    'thumbnails',
                ),

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
}
