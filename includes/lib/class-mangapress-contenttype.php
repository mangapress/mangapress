<?php
/**
 * WordPress_PostType_Framework
 *
 * So wish WordPress would drop PHP 5.2 support.
 * Namespaces would be very handy
 *
 * @package WordPress_PostType_Framework
 * @subpackage MangaPress_FrameWork_Helper
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */

/**
 * MangaPress_ContentType
 * This abstract class contains basic properties and methods
 * used by the PostType and Taxonomy classes.
 *
 * @package MangaPress_ContentType
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
abstract class MangaPress_ContentType {


	/**
	 * Object name
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * Object singular (human-readable) label
	 *
	 * @var string
	 */
	protected string $label_single;

	/**
	 * Object plural (human-readable) label
	 *
	 * @var string
	 */
	protected string $label_plural;

	/**
	 * Object arguments
	 *
	 * @var array
	 */
	protected array $args;

	/**
	 * Object init
	 *
	 * @return void
	 */
	abstract public function init();

	/**
	 * PHP5 Constructor method
	 *
	 * @param array|null $options Optional. Pass Object parameters on construct.
	 */
	public function __construct( array $options = null ) {
		if ( is_array( $options ) ) {
			$this->set_options( $options )
				->init();
		}
	}

	/**
	 * Set the object name
	 *
	 * @param string $object_name Name of object.
	 *
	 * @return MangaPress_ContentType
	 */
	public function set_name( string $object_name ): MangaPress_ContentType {
		$this->name = $object_name;

		return $this;
	}

	/**
	 * Get object name
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Set object options
	 *
	 * @param array $options Array of object options to parse.
	 *
	 * @return MangaPress_ContentType
	 */
	public function set_options( array $options ): MangaPress_ContentType {
		foreach ( $options as $option_name => $value ) {
			$method = 'set_' . $option_name;
			if ( method_exists( $this, $method ) ) {
				$this->$method( $value );
			}
		}

		return $this;
	}

	/**
	 * Set the object's singular label.
	 *
	 * @param string $object_single_name Object singular label name.
	 *
	 * @return MangaPress_ContentType
	 */
	public function set_singlename( string $object_single_name ): MangaPress_ContentType {
		$this->label_single = $object_single_name;

		return $this;
	}

	/**
	 * Set the object's plural label
	 *
	 * @param string $object_pluralname Object plural name.
	 *
	 * @return MangaPress_ContentType
	 */
	public function set_pluralname( string $object_pluralname ): MangaPress_ContentType {

		$this->label_plural = $object_pluralname;

		return $this;
	}

	/**
	 * Set object arguments
	 *
	 * @param array $args Array of arguments.
	 *
	 * @return MangaPress_ContentType
	 */
	public function set_arguments( array $args = array() ): ?MangaPress_ContentType {
		$this->args = $args;

		return $this;
	}
}
