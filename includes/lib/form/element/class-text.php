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
 * Text
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package Text
 * @version $Id$
 */
class Text extends Element {


	/**
	 * Echo form element
	 *
	 * @return string
	 */
	public function __toString() {
		$label = '';
		if ( ! empty( $this->label ) ) {
			$id    = $this->get_attributes( 'id' );
			$class = " class=\"label-$id\"";
			$label = "<label for=\"$id\"$class>$this->label</label>\r\n";
		}

		$desc        = $this->get_description();
		$description = '';
		if ( $desc ) {
			$description = "<span class=\"description\">{$desc}</span>";
		}

		$attr = $this->build_attr_string();

		$html_array['content'] = "{$label}<input type=\"text\" $attr />\r\n{$description}";

		$this->html = implode( ' ', $html_array );

		return $this->html;
	}
}
