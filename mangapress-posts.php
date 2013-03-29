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
        if (is_admin()){
            add_action("wp_ajax_{$this->_ajax_action_add_comic}", array($this, 'wp_ajax_comic_handler'));
            add_action("wp_ajax_{$this->_ajax_action_remove_comic}", array($this, 'wp_ajax_comic_handler'));
        }
        
        /*
         * Actions and filters for modifying our Edit Comics page.
         */
        add_action('manage_posts_custom_column', array($this, 'comics_headers'));
        add_filter('manage_edit-mangapress_comic_columns', array($this, 'comics_columns'));
        
        add_filter('attachment_fields_to_edit', array($this, 'attachment_fields_to_edit'), null, 2);
        add_action('admin_head-media-upload-popup', array($this, 'media_upload_popup_scripts'));
        
    }
    
    /**
     * Register the post-type
     * 
     * @return void
     */
    private function _register_post_type()
    {
        $this->_post_type = new ComicPostType();
    }

    
    /**
     * Enqueue upload scripts
     * 
     * @return void 
     */
    public function media_upload_popup_scripts()
    {
        wp_enqueue_script(MP_DOMAIN . '-media-script');
    }

    /**
     * Adds an add image link to media popup.
     * 
     * @param array $form_fields Available fields in Media Library popup.
     * @param object $post Current post object
     * @return string 
     */
    public function attachment_fields_to_edit($form_fields, $post)
    {

        if (strpos(get_post_mime_type($post->ID), 'image') === false)
            return $form_fields;

        if (!isset($_GET['post_type']) || $_GET['post_type'] !== 'mangapress_comic')
            return $form_fields;
        
        if (intval($_GET['post_id']) == 0)
            return $form_fields;
        
        $form_fields['mangapress_comic'] = array(
            'label' => __('Manga+Press', MP_DOMAIN),
            'input' => 'html',
        );
        
        $parent_post_id = intval($_GET['post_id']);
        $nonce = wp_create_nonce($this->_nonce_insert_comic);
        $fields = "<p>"
                . "<a href=\"#\" data-post-parent=\"{$parent_post_id}\" " 
                . "data-attachment-id=\"{$post->ID}\" data-nonce=\"{$nonce}\" " 
                . "class=\"manga-press-add-comic-link\">Use As Comic Image</a>"
                . "</p>";
        
        $form_fields['mangapress_comic']['html'] = $fields;
        
        return $form_fields;
    }
    
    /**
     * Handler function for Ajax calls.
     * 
     * @return string 
     */
    public function wp_ajax_comic_handler() 
    {

        header('Content-type: application/json');
        
        $nonce_action = ($_POST['action'] == 'add-comic')
                            ? $this->_nonce_insert_comic
                            : "set_comic_thumbnail-{$_POST['post_parent']}";

        if (!wp_verify_nonce($_POST['nonce'], $nonce_action)) {
            // send a JSON response
            echo json_encode(array(
                'error' => 'invalid-nonce',
                'msg'   => 'Nonce has either expired or is invalid. '
                           . 'Please re-open Media Library modal and try again.'
            ));
            exit();
        }
        
        if ($_POST['attachment_id'] == '' || $_POST['attachment_id'] == 0) {
            
            echo json_encode(array(
                'error' => 'no-attachment-id',
                'msg'   => 'Attachment ID is blank.'
            ));
            exit();
        }
        
        if ($_POST['action'] == 'add-comic') {
            $html = $this->_post_type->admin_post_comic_html($_POST['attachment_id'], $_POST['post_parent']);
            $this->_set_post_comic_image($_POST['attachment_id'], $_POST['post_parent']);
        } else {
            $html = $this->_post_type->admin_post_comic_html(null, $_POST['post_parent']);
            $this->_delete_post_comic_image($_POST['post_parent']);
        }
        
        echo json_encode(array(
            'html'        => $html,
            'post_parent' => intval($_POST['post_parent']),
        ));
        
        die();
            
    }
    
    /**
     * Private helper function for setting comic thumbnail as featured image.
     * 
     * @global array $_wp_additional_image_sizes
     * @param integer $thumbnail_id
     * @param integer $post_parent
     * @return boolean
     */    
    private function _set_post_comic_image($thumbnail_id, $post_parent)
    {
        
        $attachment_post = get_post($thumbnail_id);
        $attachment_post->post_parent = $post_parent;
        
        wp_update_post($attachment_post);
        
        return set_post_thumbnail($post_parent, $thumbnail_id);
    }
    
    /**
     * Removes the post from featured image when deleted.
     * 
     * @param integer $post_parent
     * @return boolean
     */
    private function _delete_post_comic_image($post_parent)
    {
        $thumbnail_id = get_post_thumbnail_id($post_parent);
        
        $attachment_post = get_post($thumbnail_id);
        $attachment_post->post_parent = 0;
        
        wp_update_post($attachment_post);
        
        return delete_post_thumbnail($post_parent);
        
    }
    
    /**
     * mpp_custom_columns()
     *
     * @since 2.7
     *
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
     * mpp_comic_columns()
     *
     * @since 2.7
     *
     */
    public function comics_columns($columns)
    {

        $columns = array(
                "cb"          => "<input type=\"checkbox\" />",
                "thumbnail"   => "Thumbnail",
                "title"       => "Comic Title",
                "series"      => "Series",
                "description" => "Description",
        );

        return $columns;

    }
}
