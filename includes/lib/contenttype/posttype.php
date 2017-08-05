<?php
/**
 * WordPress_PostType_Framework
 *
 * @package WordPress_PostType_Framework
 * @subpackage MangaPress_PostType
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 */
namespace MangaPress\ContentType;
use MangaPress\ContentType;
/**
 * MangaPress_PostType
 *
 * @package MangaPress_PostType
 * @author Jess Green <jgreen at psy-dreamer.com>
 */
class PostType extends ContentType
{

    /**
     * PostType Capabilities
     * @var array
     */
    protected $_capabilities = array(
        'edit_post',
        'read_post',
        'delete_post',
        'edit_posts',
        'edit_others_posts',
        'publish_posts',
        'read_private_posts',
    );

    /**
     * Taxonomies attached to PostType
     *
     * @var array
     */
    protected $_taxonomies   = array();

    /**
     * Object arguments
     *
     * @var array
     */
    protected $_args         = array(
        'labels'               => '',
        'description'          => '',
        'public'               => true,
        'publicly_queryable'   => true,
        'exclude_from_search'  => false,
        'show_ui'              => true,
        'show_in_menu'         => true,
        'menu_position'        => 5,
        'menu_icon'            => null,
        'capability_type'      => 'post',
        'hierarchical'         => false,
        'supports'             => false,
        'register_meta_box_cb' => null,
        'taxonomies'           => array(),
        'permalink_epmask'     => EP_PERMALINK,
        'has_archive'          => false,
        'rewrite'              => true,
        'can_export'           => true,
        'show_in_nav_menus'    => true,
    );

    /**
     * Object init
     *
     * @return void
     */
    public function init()
    {

        register_post_type($this->_name, $this->_args);

    }


    /**
     * Set object arguments
     *
     * @param array $args Array of arguments. Optional
     * @return JesGS_PostType
     */
    public function set_arguments($args = array())
    {
        $args = array_merge($this->_args, $args);
        extract($args);

        $labels
            = array(
                'name'               => __($this->_label_plural, $this->_textdomain),
                'singular_name'      => __($this->_label_single, $this->_textdomain),
                'add_new'            => __('Add New', $this->_textdomain),
                'add_new_item'       => sprintf(__('Add New %s', $this->_textdomain), $this->_label_single),
                'edit_item'          => sprintf(__('Edit %s', $this->_textdomain), $this->_label_single),
                'view_item'          => sprintf(__('View %s', $this->_textdomain), $this->_label_single),
                'search_items'       => sprintf(__('Search %s', $this->_textdomain), $this->_label_plural),
                'not_found'          => sprintf(__('%s not found', $this->_textdomain), $this->_label_single),
                'not_found_in_trash' => sprintf(__('%s not found in Trash', $this->_textdomain), $this->_label_single),
                'parent_item_colon'  => sprintf(__('%s: ', $this->_textdomain), $this->_label_single),
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
                'hierarchical'         => $hierarchical,
                'supports'             => $supports,
                'register_meta_box_cb' => $register_meta_box_cb,
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

    /**
     * Set object taxonomies
     *
     * @param array $taxonomies
     * @return JesGS_PostType
     */
    public function set_taxonomies($taxonomies)
    {
        $this->_taxonomies = $taxonomies;

        return $this;
    }

}