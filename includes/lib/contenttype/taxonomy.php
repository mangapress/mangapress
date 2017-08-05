<?php
/**
 * WordPress_PostType_Framework
 *
 * @package WordPress_PostType_Framework
 * @subpackage MangaPress_Taxonomy
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
namespace MangaPress\ContentType;
use MangaPress\ContentType;

/**
 * MangaPress_Taxonomy
 *
 * @package MangaPress_Taxonomy
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class Taxonomy extends ContentType
{

    /**
     * Objects that support this taxonomy
     * @var array
     */
    protected $_object_types  = array();

    /**
     * Object arguments
     *
     * @var array
     */
    protected $_args = array(
        'labels'                => '',
        'public'                => true,
        'can_export'            => true,
        'show_in_nav_menus'     => true,
        'show_ui'               => true,
        'show_tagcloud'         => false,
        'hierarchical'          => false,
        'update_count_callback' => '',
        'rewrite'               => true,
        'query_var'             => true,
        'capabilities'          => array(),
    );

    /**
     * Init object
     */
    public function init()
    {
        register_taxonomy($this->_name, $this->_object_types, $this->_args);
    }

    /**
     * Set object arguments
     *
     * @param array $args
     * @return JesGS_Taxonomy
     */
    public function set_arguments($args = array())
    {

        $args = array_merge($this->_args, $args);
        extract($args);

        $args
            = array(
                'labels' => array(
                    'name'                       => $this->_label_plural,
                    'singular_name'              => $this->_label_single,
                    'search_items'               => sprintf(__('Search %s', $this->_textdomain), $this->_label_plural),
                    'popular_items'              => sprintf(__('Popular %s', $this->_textdomain), $this->_label_plural),
                    'all_items'                  => sprintf(__('All %s', $this->_textdomain), $this->_label_plural),
                    'parent_item'                => sprintf(__('Parent %s', $this->_textdomain), $this->_label_single),
                    'parent_item_colon'          => sprintf(__('Parent %s:: ', $this->_textdomain), $this->_label_single),
                    'edit_item'                  => sprintf(__('Edit %s', $this->_textdomain), $this->_label_single),
                    'update_item'                => sprintf(__('Update %s', $this->_textdomain), $this->_label_single),
                    'add_new_item'               => sprintf(__('Add New %s', $this->_textdomain), $this->_label_single),
                    'new_item_name'              => sprintf(__('New %s name', $this->_textdomain), $this->_label_single),
                    'separate_items_with_commas' => sprintf(__('Separate %s with commas', $this->_textdomain), $this->_label_plural),
                    'add_or_remove_items'        => sprintf(__('Add or remove %s', $this->_textdomain), $this->_label_plural),
                    'choose_from_most_used'      => sprintf(__('Choose from most used %s', $this->_textdomain), $this->_label_plural),
                ),
                'public'                => $public,
                'can_export'            => $can_export,
                'show_in_nav_menus'     => $show_in_nav_menus,
                'show_ui'               => $show_ui,
                'show_tagcloud'         => $show_tagcloud,
                'hierarchical'          => $hierarchical,
                'update_count_callback' => '',
                'rewrite'               => $rewrite,
                'query_var'             => $query_var,
                'capabilities'          => $capabilities,
            );

        $this->_args = $args;

        return $this;
    }

    /**
     * Set taxonomy objects
     *
     * @param array $object_types
     * @return JesGS_Taxonomy
     */
    public function set_objects($object_types)
    {
        $this->_object_types  = $object_types;

        return $this;
    }
}