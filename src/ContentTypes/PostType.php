<?php
/**
 * Post-type builder class
 *
 * @package MangaPress\ContentTypes
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\ContentTypes;

/**
 * Class PostType
 * @package MangaPress\ContentTypes
 */
class PostType implements ContentType
{
    use Parameters;

    /**
     * PostType Capabilities
     *
     * @var array
     */
    protected $capabilities = [
        'edit_post',
        'read_post',
        'delete_post',
        'edit_posts',
        'edit_others_posts',
        'publish_posts',
        'read_private_posts',
    ];

    /**
     * Taxonomies attached to PostType
     *
     * @var array
     */
    protected $taxonomies = [];

    /**
     * Object arguments
     *
     * @var array
     */
    protected $args = [
        'labels'                => '',
        'description'           => '',
        'public'                => true,
        'publicly_queryable'    => true,
        'exclude_from_search'   => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => null,
        'capability_type'       => 'post',
        'hierarchical'          => false,
        'supports'              => false,
        'register_meta_box_cb'  => null,
        'taxonomies'            => [],
        'permalink_epmask'      => EP_PERMALINK,
        'has_archive'           => false,
        'rewrite'               => true,
        'can_export'            => true,
        'show_in_nav_menus'     => true,
        'show_in_rest'          => false,
        'rest_base'             => false,
        'rest_controller_class' => false,
    ];

    /**
     * Register PostType
     */
    public function register_content_type()
    {
        register_post_type($this->name, $this->args);
    }

    /**
     * Set PostType arguments
     *
     * @param array $args
     * @return $this
     */
    public function set_arguments($args = [])
    {
        $args = wp_parse_args($args, $this->args);

        /**
         * @var $labels
         * @var $description
         * @var $public
         * @var $publicly_queryable
         * @var $exclude_from_search
         * @var $show_ui
         * @var $show_in_menu
         * @var $menu_position
         * @var $menu_icon
         * @var $capability_type
         * @var $hierarchical
         * @var $supports
         * @var $register_meta_box_cb
         * @var $taxonomies
         * @var $has_archive
         * @var $rewrite
         * @var $can_export
         * @var $show_in_nav_menus
         * @var $show_in_rest
         * @var $rest_base
         * @var $rest_controller_class
         */
        extract($args);

        $labels = [
            'name'               => __($this->label_plural, $this->textdomain),
            'singular_name'      => __($this->label_single, $this->textdomain),
            'add_new'            => __('Add New', $this->textdomain),
            'add_new_item'       => sprintf(__('Add New %s', $this->textdomain), $this->label_single),
            'edit_item'          => sprintf(__('Edit %s', $this->textdomain), $this->label_single),
            'view_item'          => sprintf(__('View %s', $this->textdomain), $this->label_single),
            'search_items'       => sprintf(__('Search %s', $this->textdomain), $this->label_plural),
            'not_found'          => sprintf(__('%s not found', $this->textdomain), $this->label_single),
            'not_found_in_trash' => sprintf(__('%s not found in Trash', $this->textdomain), $this->label_single),
            'parent_item_colon'  => sprintf(__('%s: ', $this->textdomain), $this->label_single),
        ];

        $args =
            [
                'labels'                => $labels,
                'description'           => $description,
                'public'                => $public,
                'publicly_queryable'    => $publicly_queryable,
                'exclude_from_search'   => $exclude_from_search,
                'show_ui'               => $show_ui,
                'show_in_menu'          => $show_in_menu,
                'menu_position'         => $menu_position,
                'menu_icon'             => $menu_icon,
                'capability_type'       => $capability_type,
                'hierarchical'          => $hierarchical,
                'supports'              => $supports,
                'register_meta_box_cb'  => $register_meta_box_cb,
                'taxonomies'            => $taxonomies,
                'permalink_epmask'      => EP_PERMALINK,
                'has_archive'           => $has_archive,
                'rewrite'               => $rewrite,
                'can_export'            => $can_export,
                'show_in_nav_menus'     => $show_in_nav_menus,
                'show_in_rest'          => $show_in_rest,
                'rest_base'             => $rest_base,
                'rest_controller_class' => $rest_controller_class,
            ];

        $this->args = $args;

        return $this;
    }

    /**
     * Set object taxonomies
     *
     * @param array $taxonomies
     * @return $this
     */
    public function set_taxonomies($taxonomies)
    {
        $this->taxonomies = $taxonomies;

        return $this;
    }
}
