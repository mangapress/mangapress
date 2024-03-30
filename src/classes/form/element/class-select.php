<?php
/**
 * Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */

namespace MangaPress\Form\Element;
use MangaPress\Form\Element as Element;

/**
 * Select
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package Select
 * @version $Id$
 */
class Select extends Element {

	/**
	 * Options
	 *
	 * @var array
	 */
	protected array $options = array();

	/**
	 * Build attribute string for select element.
	 *
	 * @return string
	 */
	public function build_attr_string(): string {
		$attr_arr = array();
		foreach ( $this->attr as $name => $value ) {
			$attr_arr[] = vsprintf( '%1$s="%2$s"', array( esc_attr( $name ), esc_attr( $value ) ) );
		}

		return implode( ' ', $attr_arr );
	}

	/**
	 * Echo form element
	 *
	 * @return string
	 */
	public function __toString() {
		$options = $this->get_default();

		$attr = $this->build_attr_string();

		$value       = $this->get_value();
		$options_str = '';
		foreach ( $options as $option_val => $option_text ) {
			$selected     = selected( $value, $option_val, false );
			$options_str .= "<option value=\"$option_val\" $selected>{$option_text}</option>";
		}

		$this->html = "<select $attr>\n$options_str</select> " . $this->get_description();

		return $this->html;
	}

	/**
	 * Set default values
	 *
	 * @param array $defaults Option values to set.
	 * @return Element
	 */
	public function set_default( $defaults ): Element {
		foreach ( $defaults as $key => $value ) {
			$this->options[ $key ] = $value;
		}

		return $this;
	}

	/**
	 * Get default values
	 *
	 * @return array
	 */
	public function get_default(): array {
		return $this->options;
	}
}
