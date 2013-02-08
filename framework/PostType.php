<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */

/**
 * MangaPress_PostType
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress_PostType
 * @version $Id$
 *
 * @todo Add meta box callbacks
 */
class MangaPress_PostType extends MangaPress_FrameWork_Helper
{
    /**
     * Default post-type capabilities. Not used.
     *
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
     * Taxonomies associated with post-type
     *
     * @var array
     */
    protected $_taxonomies   = array();

    /**
     * Default post-type arguments
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
        'menu_icon'            => '',
        'capability_type'      => 'post',
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

    /**
     * Set default fields
     * See {@link http://codex.wordpress.org/Function_Reference/register_post_type}
     *
     * @var array
     */
    protected $_supports     = array('title');

    /**
     * View object for enqueuing JS/CSS
     * 
     * @var MangaPress_View
     */
    protected $_view;
    /**
     * Init. Register post-type. Called by init hook
     *
     * @return void
     */
    public function init()
    {
        register_post_type($this->_name, $this->_args);
    }

    /**
     * Set View object for enqueing JS/CSS files
     *
     * @param MangaPress_View $view
     * @return \MangaPress_PostType
     */
    public function set_view($view)
    {
        $this->_view = $view;

        return $this;
    }

    /**
     * Set post-type arguments
     *
     * @global string $plugin_dir
     * @param array $args
     * @return \MangaPress_PostType
     */
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

    /**
     * Set post-type taxonomies
     *
     * @param array $taxonomies
     * @return \MangaPress_PostType
     */
    public function set_taxonomies($taxonomies)
    {
        $this->_taxonomies = $taxonomies;

        return $this;
    }

    /**
     * Set the default fields that the post-type supports
     *
     * @param array $supports
     * @return \MangaPress_PostType
     */
    public function set_support($supports)
    {
        $this->_supports = $supports;

        return $this;
    }

    /**
     * Meta-box callback class
     * @return void
     */
    public function meta_box_cb()
    {
        // override this method in extending class
    }
}
