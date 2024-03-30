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
 * Radio
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package Radio
 * @version $Id$
 */
class Radio extends Element {


	/**
	 * Echo form element
	 *
	 * @return string
	 */
	public function __toString() {
		$attr    = $this->build_attr_string();
		$checked = checked( $this->get_default(), $this->get_value(), false );

		$html = $this->get_label()
				. "<input type=\"radio\" $attr $checked />\r\n"
				. $this->get_description();

		$this->html = $html;

		return $this->html;
	}
}
