<?php
/**
 * WordPress_PostType_Framework
 *
 * @package WordPress_PostType_Framework
 * @subpackage MangaPress_PostType
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */

/**
 * MangaPress_PostType
 *
 * @package MangaPress_PostType
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class MangaPress_PostType extends MangaPress_ContentType {


	/**
	 * PostType Capabilities
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
	 * Taxonomies attached to PostType
	 *
	 * @var array
	 */
	protected $taxonomies = array();

	/**
	 * Object arguments
	 *
	 * @var array
	 */
	protected array $args = array(
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
	public function init() {

		register_post_type( $this->name, $this->args );
	}


	/**
	 * Set object arguments
	 *
	 * @param array $args Array of arguments. Optional.
	 *
	 * @return MangaPress_PostType
	 */
	public function set_arguments( array $args = array() ): MangaPress_PostType {
		$args = array_merge( $this->args, $args );
		extract( $args ); // @phpcs:ignore -- disabling warning for now.

		$labels
			= array(
				'name'               => $this->label_plural,
				'singular_name'      => $this->label_single,
				// translators: %s is the singular form of the object label defined by the label_single object property.
				'add_new'            => __( 'Add New', 'mangapress' ),
				// translators: %s is the singular form of the object label defined by the label_single object property.
				'add_new_item'       => sprintf( __( 'Add New %s', 'mangapress' ), $this->label_single ),
				// translators: %s is the singular form of the object label defined by the label_single object property.
				'edit_item'          => sprintf( __( 'Edit %s', 'mangapress' ), $this->label_single ),
				// translators: %s is the singular form of the object label defined by the label_single object property.
				'view_item'          => sprintf( __( 'View %s', 'mangapress' ), $this->label_single ),
				// translators: %s is the plural form of the object label set by the label_plural object property.
				'search_items'       => sprintf( __( 'Search %s', 'mangapress' ), $this->label_plural ),
				// translators: %s is the singular form of the object label defined by the label_single object property.
				'not_found'          => sprintf( __( '%s not found', 'mangapress' ), $this->label_single ),
				// translators: %s is the singular form of the object label defined by the label_single object property.
				'not_found_in_trash' => sprintf( __( '%s not found in Trash', 'mangapress' ), $this->label_single ),
				// translators: %s is the singular form of the object label defined by the label_single object property.
				'parent_item_colon'  => sprintf( __( '%s: ', 'mangapress' ), $this->label_single ),
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

		$this->args = $args;

		return $this;
	}

	/**
	 * Set object taxonomies
	 *
	 * @param array $taxonomies Array of taxonomies to assign to post-type.
	 *
	 * @return MangaPress_PostType
	 */
	public function set_taxonomies( array $taxonomies ): MangaPress_PostType {
		$this->taxonomies = $taxonomies;

		return $this;
	}
}
