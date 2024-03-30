<?php
/**
 * Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */

namespace MangaPress\Form\Element;

use MangaPress\Form\Element;

/**
 * Checkbox
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package Checkbox
 * @version $Id$
 */
class Checkbox extends Element {

	/**
	 * Display form element
	 *
	 * @return string
	 */
	public function __toString() {
		$attr    = $this->build_attr_string();
		$checked = checked( $this->get_default(), $this->get_value(), false );

		$html = $this->get_label()
				. "<input type=\"checkbox\" $attr $checked />\r\n"
				. $this->get_description();

		$this->html = $html;

		return $this->html;
	}
}
