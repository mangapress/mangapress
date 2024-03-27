<?php
/**
 * MangaPress Element class
 *
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */

/**
 * MangaPress_Element
 * Abstract class used to define basic functionality for extending classes
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress_Element
 * @version $Id$
 */
class MangaPress_Element {

	/**
	 * Elements HTML attributes array
	 *
	 * @var array
	 */
	protected array $attr;

	/**
	 * Element label text
	 *
	 * @var string
	 */
	protected string $label;

	/**
	 * Name attribute
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * Default value
	 *
	 * @var mixed
	 */
	protected $default_value;

	/**
	 * Value (if different from default)
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * The element data-type. Determines validation
	 *
	 * @var string
	 */
	protected string $data_type;

	/**
	 * Object html
	 *
	 * @var string
	 */
	protected string $html;

	/**
	 * Description field
	 *
	 * @var string
	 */
	protected string $description;

	/**
	 * PHP5 constructor method.
	 *
	 * @param ?array $options Array of object options.
	 *
	 * @return void
	 */
	public function __construct( ?array $options = null ) {
		if ( is_array( $options ) ) {
			$this->set_options( $options );
		}
	}

	/**
	 * Set options
	 *
	 * @param array $options Array of element options.
	 *
	 * @return \MangaPress_Element
	 */
	public function set_options( array $options ): MangaPress_Element {
		foreach ( $options as $option_name => $value ) {
			$method = 'set_' . $option_name;
			if ( method_exists( $this, $method ) ) {
				$this->$method( $value );
			}
		}

		return $this;
	}

	/**
	 * Add attributes to element
	 *
	 * @param array $attributes Array of attributes.
	 * @return \MangaPress_Element
	 */
	public function add_attributes( array $attributes = array() ): MangaPress_Element {
		foreach ( $attributes as $attr => $value ) {
			$this->set_attributes( $attr );
		}

		return $this;
	}

	/**
	 * Get attributes as represented by $key
	 *
	 * @param string $key Attribute to retrieve.
	 *
	 * @return null|string
	 */
	public function get_attributes( string $key ): ?string {
		if ( ! isset( $this->attr[ $key ] ) ) {
			return null;
		}

		return $this->attr[ $key ];
	}

	/**
	 * Set attributes
	 *
	 * @param array $attr Array of element attributes.
	 *
	 * @return \MangaPress_Element
	 */
	public function set_attributes( array $attr ): MangaPress_Element {
		foreach ( $attr as $key => $value ) {
			$this->attr[ $key ] = $value;
		}

		return $this;
	}

	/**
	 * Set label
	 *
	 * @param string $text Element label text.
	 *
	 * @return \MangaPress_Element
	 */
	public function set_label( string $text = '' ): MangaPress_Element {

		$this->label = $text;

		return $this;
	}

	/**
	 * Set default value
	 *
	 * @param mixed $default_value Default value of element.
	 * @return \MangaPress_Element
	 */
	public function set_default( $default_value ): MangaPress_Element {
		$this->default_value = $default_value;

		return $this;
	}

	/**
	 * Get default value
	 *
	 * @return mixed
	 */
	public function get_default() {
		return $this->default_value;
	}

	/**
	 * Get value attribute
	 *
	 * @return mixed
	 */
	public function get_value() {
		return $this->attr['value'];
	}

	/**
	 * Set the data type
	 *
	 * @param string $data_type Data type of element.
	 *
	 * @return \MangaPress_Element
	 */
	public function set_data_type( string $data_type ): MangaPress_Element {
		$this->data_type = $data_type;

		return $this;
	}

	/**
	 * Get name attribute
	 *
	 * @return string
	 */
	public function get_name(): ?string {
		return $this->get_attributes( 'name' );
	}

	/**
	 * Set description
	 *
	 * @param string $description Form element description.
	 *
	 * @return \MangaPress_Element
	 */
	public function set_description( string $description ): MangaPress_Element {
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Build the attribute string
	 *
	 * @return string
	 */
	public function build_attr_string() {
		$attr_arr = array();
		foreach ( $this->attr as $name => $value ) {
			$attr_arr[] = "{$name}=\"{$value}\"";
		}

		$attr = implode( ' ', $attr_arr );

		return $attr;
	}
}

require_once MP_ABSPATH . '/includes/lib/form/element/class-mangapress-checkbox.php';
require_once MP_ABSPATH . '/includes/lib/form/element/class-mangapress-radio.php';
require_once MP_ABSPATH . '/includes/lib/form/element/class-mangapress-select.php';
require_once MP_ABSPATH . '/includes/lib/form/element/class-mangapress-text.php';
require_once MP_ABSPATH . '/includes/lib/form/element/class-mangapress-textarea.php';
