<?php
/**
 * PostType_Class
 * Change PostType to match the name of your custom post type. Should be some-
 * thing like MyPostType_Class.
 *
 * @package PostType_Class
 * @subpackage PluginFramework
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id: post-type-class.php 4 2011-06-15 00:50:16Z ardath.ksheyna78 $
 *
 * @todo Add meta box callbacks
 */
class PostType extends FrameWork_Helper
{
    protected $_name;
    protected $_label_single;
    protected $_label_plural;
    protected $_capabilities = array(
        'edit_post',
        'read_post',
        'delete_post',
        'edit_posts',
        'edit_others_posts',
        'publish_posts',
        'read_private_posts',
    );
    protected $_taxonomies   = array();

    protected $_args         = array(
        'labels'               => '',
        'description'          => '',
        'public'               => true,
        'publicly_queryable'   => true,
        'exclude_from_search'  => false,
        'show_ui'              => true,
        'show_in_menu'         => true,
        'menu_position'        => 5,
        'menu_icon'            => '',
        'capability_type'      => 'post',
        //'capabilities'         => '',
        //'map_meta_cap'         => false,
        'hierarchical'         => false,
        'supports'             => '',
        'register_meta_box_cb' => '',
        'taxonomies'           => array(),
        'permalink_epmask'     => EP_PERMALINK,
        'has_archive'          => false,
        'rewrite'              => true,
        'can_export'           => true,
        'show_in_nav_menus'    => true,
    );

    protected $_supports     = array('title');

    protected $_nonce;

    protected $_metaboxes    = array();

    protected $_styles       = array();

    protected $_scripts      = array();
    
    protected $_rewrite_rules = array();
    
    protected $_templates = array();
    
    protected $_view;


    public function init()
    {
                
        register_post_type($this->_name, $this->_args);
                
        add_action('generate_rewrite_rules', array($this, 'rewrite'));
        add_action('template_include', array($this, 'template_include'));
        
    }

    public function set_view($view)
    {
        $this->_view = $view;
        
        return $this;
    }
    
    public function set_arguments($args)
    {
        global $plugin_dir;

        $args = array_merge($this->_args, $args);
        extract($args);

        $labels
            = array(
                'name'               => $this->_label_plural,
                'singular_name'      => $this->_label_single,
                'add_new'            => __('Add New ' . $this->_label_single, $plugin_dir),
                'add_new_item'       => __('Add New ' . $this->_label_single, $plugin_dir),
                'edit_item'          => __('Edit ' . $this->_label_single, $plugin_dir),
                'view_item'          => __('View ' . $this->_label_single, $plugin_dir),
                'search_items'       => __('Search ' . $this->_label_single, $plugin_dir),
                'not_found'          => __($this->_label_single . ' not found', $plugin_dir),
                'not_found_in_trash' => __($this->_label_single . ' not found in Trash', $plugin_dir),
                'parent_item_colon'  => __($this->_label_single . ': ', $plugin_dir),
            );


        $args =
            array(
                'labels'               => $labels,
                'description'          => $description,
                'public'               => $public,
                'publicly_queryable'   => $publicly_queryable,
                'exclude_from_search'  => $exclude_from_search,
                'show_ui'              => $show_ui,
                'show_in_menu'         => $show_in_menu,
                'menu_position'        => $menu_position,
                'menu_icon'            => $menu_icon,
                'capability_type'      => $capability_type,
                //'capabilities'         => $this->_capabilities,
                //'map_meta_cap'         => $map_meta_cap,
                'hierarchical'         => $hierarchical,
                'supports'             => $supports,
                'register_meta_box_cb' => array($this, 'meta_box_cb'),
                'taxonomies'           => $taxonomies,
                'permalink_epmask'     => EP_PERMALINK,
                'has_archive'          => $has_archive,
                'rewrite'              => $rewrite,
                'can_export'           => $can_export,
                'show_in_nav_menus'    => $show_in_nav_menus,
            );

        $this->_args = $args;

        return $this;
    }

    public function set_taxonomies($taxonomies)
    {
        $this->_taxonomies = $taxonomies;

        return $this;
    }

    public function set_support($supports)
    {
        $this->_supports = $supports;

        return $this;
    }

    public function set_metaboxes($meta_boxes = array())
    {
        $this->_metaboxes = $meta_boxes;

        return $this;
    }
    
    public function set_templates($templates = array())
    {
        $this->_templates = $templates;
        
        return $this;
    }
    
    public function meta_box_cb()
    {
        global $post;
        
        // rewrite this function
    }
    
    /**
     * Set an array of custom rewrite rules for post-type
     * 
     * @param type $rules
     * @return PostType_Class 
     */
    public function set_rewrite_rules($rules = array())
    {
        $this->_rewrite_rules = $rules;
        
        return $this;
    }

    /**
     * Handles saving of meta-data for post-type
     * 
     * @param integer $post_id
     * @return int|array
     */
    public function save_post($post_id)
    {
        
        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        if ( !wp_verify_nonce( $_POST[$this->_name . '_nonce'], $this->_name . '-nonce' ))
            return $post_id;

        //
        // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
        // to do anything
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
            return $post_id;

        // Check permissions
        if ( $this->_name == $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_' . $this->_args['capability_type'], $post_id ) ) {
                return $post_id;
            }
        }
        
        // save data here
        // for now, no sanitization
        $metabox_ID = $this->_metaboxes[0]->_form_properties['id'];
        $meta_key = "{$metabox_ID}_meta";
        
        $data = $_POST[$metabox_ID];

        if (!add_post_meta($post_id, $meta_key, $data, true)) {
            update_post_meta($post_id, $meta_key, $data);
        }

        return $data;

    }
        
    /**
     *
     * @global type $wp_rewrite 
     * 
     * @return void
     */
    public function rewrite()
    {
        global $wp_rewrite;        
        
        $rules = array_merge($this->_rewrite_rules, $wp_rewrite->rules);
        
        $wp_rewrite->rules = apply_filters('rewrite_rules_array', $rules);
    }
    
    /**
     * Returns default templates or loads a specified template when found.
     * 
     * @param array $template Array of template paths relative to current theme.
     * @return array
     */
    public function template_include($template)
    {       
//        if ('' == locate_template($this->_templates, true)) {
//            load_template(MP_ABSPATH . 'templates/latest-comic.php');
//        }
        
        return $template;
    }
}
?>