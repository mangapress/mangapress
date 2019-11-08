<?php
/**
 * Taxonomy Builder class
 *
 * @package MangaPress\ContentTypes
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\ContentTypes;

/**
 * Class Taxonomy
 * @package MangaPress\ContentTypes
 */
class Taxonomy implements ContentType
{
    use Parameters;

    /**
     * Objects that support this taxonomy
     *
     * @var array
     */
    protected $object_types = [];

    /**
     * Object arguments
     *
     * @var array
     */
    protected $args = [
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
        'capabilities'          => [],
        'show_in_rest'          => false,
        'rest_base'             => false,
        'rest_controller_class' => false,
    ];

    public function register_content_type()
    {
        register_taxonomy($this->name, $this->object_types, $this->args);
    }


    /**
     * Set object arguments
     *
     * @param array $args
     * @return Taxonomy
     */
    public function set_arguments($args = [])
    {

        $args = wp_parse_args($args, $this->args);

        /**
         * @var $public
         * @var $can_export
         * @var $show_in_nav_menus
         * @var $show_ui
         * @var $show_tagcloud
         * @var $hierarchical
         * @var $rewrite
         * @var $query_var
         * @var $capabilities
         * @var $show_in_rest
         * @var $rest_base
         * @var $rest_controller_class
         */
        extract($args);

        $args = [
            'labels'                => [
                'name'                       => $this->label_plural,
                'singular_name'              => $this->label_single,
                'search_items'               => sprintf(__('Search %s', $this->textdomain), $this->label_plural),
                'popular_items'              => sprintf(__('Popular %s', $this->textdomain), $this->label_plural),
                'all_items'                  => sprintf(__('All %s', $this->textdomain), $this->label_plural),
                'parent_item'                => sprintf(__('Parent %s', $this->textdomain), $this->label_single),
                'parent_item_colon'          => sprintf(__('Parent %s:: ', $this->textdomain), $this->label_single),
                'edit_item'                  => sprintf(__('Edit %s', $this->textdomain), $this->label_single),
                'update_item'                => sprintf(__('Update %s', $this->textdomain), $this->label_single),
                'add_new_item'               => sprintf(__('Add New %s', $this->textdomain), $this->label_single),
                'new_item_name'              => sprintf(__('New %s name', $this->textdomain), $this->label_single),
                'separate_items_with_commas' => sprintf(
                    __('Separate %s with commas', $this->textdomain),
                    $this->label_plural
                ),
                'add_or_remove_items'        => sprintf(__('Add or remove %s', $this->textdomain), $this->label_plural),
                'choose_from_most_used'      => sprintf(
                    __('Choose from most used %s', $this->textdomain),
                    $this->label_plural
                ),
            ],
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
            'show_in_rest'          => $show_in_rest,
            'rest_base'             => $rest_base,
            'rest_controller_class' => $rest_controller_class,
        ];

        $this->args = $args;

        return $this;
    }

    /**
     * Set taxonomy objects
     *
     * @param array $object_types
     * @return Taxonomy
     */
    public function set_objects($object_types)
    {
        $this->object_types = $object_types;

        return $this;
    }
}
