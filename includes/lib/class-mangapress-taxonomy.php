<?php
/**
 * WordPress_PostType_Framework
 *
 * @package WordPress_PostType_Framework
 * @subpackage MangaPress_Taxonomy
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */

/**
 * MangaPress_Taxonomy
 *
 * @package MangaPress_Taxonomy
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class MangaPress_Taxonomy extends MangaPress_ContentType {


	/**
	 * Objects that support this taxonomy
	 *
	 * @var array
	 */
	protected array $object_types = array();

	/**
	 * Object arguments
	 *
	 * @var array
	 */
	protected array $args = array(
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
	public function init() {
		register_taxonomy( $this->name, $this->object_types, $this->args );
	}

	/**
	 * Set object arguments
	 *
	 * @param array $args Array of object arguments.
	 *
	 * @return MangaPress_Taxonomy
	 */
	public function set_arguments( array $args = array() ): MangaPress_Taxonomy {

		$args = array_merge( $this->args, $args );
		extract( $args ); // @phpcs:ignore -- suppressing for now.

		$args
			= array(
				'labels'                => array(
					'name'                       => $this->label_plural,
					'singular_name'              => $this->label_single,
					// translators: %s is the singular form of the object label defined by the label_single object property.
					'search_items'               => sprintf( __( 'Search %s', 'mangapress' ), $this->label_plural ),
					// translators: %s is the plural form of the object label set by the label_plural object property.
					'popular_items'              => sprintf( __( 'Popular %s', 'mangapress' ), $this->label_plural ),
					// translators: %s is the plural form of the object label.
					'all_items'                  => sprintf( __( 'All %s', 'mangapress' ), $this->label_plural ),
					// translators: %s is the singular form of the object label defined by the label_single property.
					'parent_item'                => sprintf( __( 'Parent %s', 'mangapress' ), $this->label_single ),
					// translators: %s is the singular form of the object label defined by the label_single property.
					'parent_item_colon'          => sprintf( __( 'Parent %s:: ', 'mangapress' ), $this->label_single ),
					// translators: %s is the singular form of the object label defined by the label_single property.
					'edit_item'                  => sprintf( __( 'Edit %s', 'mangapress' ), $this->label_single ),
					// translators: %s is the singular form of the object label defined by the label_single property.
					'update_item'                => sprintf( __( 'Update %s', 'mangapress' ), $this->label_single ),
					// translators: %s is the singular form of the object label defined by the label_single property.
					'add_new_item'               => sprintf( __( 'Add New %s', 'mangapress' ), $this->label_single ),
					// translators: %s is the singular form of the object label defined by the label_single property.
					'new_item_name'              => sprintf( __( 'New %s name', 'mangapress' ), $this->label_single ),
					// translators: %s is the plural form of the object label set by the label_plural object property.
					'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'mangapress' ), $this->label_plural ),
					// translators: %s is the plural form of the object label set by the label_plural object property.
					'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'mangapress' ), $this->label_plural ),
					// translators: %s is the plural form of the object label set by the label_plural object property.
					'choose_from_most_used'      => sprintf( __( 'Choose from most used %s', 'mangapress' ), $this->label_plural ),
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

		$this->args = $args;

		return $this;
	}

	/**
	 * Set taxonomy objects
	 *
	 * @param array $object_types Objects to set.
	 *
	 * @return MangaPress_Taxonomy
	 */
	public function set_objects( array $object_types ): MangaPress_Taxonomy {
		$this->object_types = $object_types;

		return $this;
	}
}
