<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */

/**
 * MangaPress_Radio
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress_Radio
 * @version $Id$
 */
class MangaPress_Radio extends MangaPress_Element {


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

		$desc = $this->get_description();
		if ( $desc ) {
			$description = "<span class=\"description\">{$desc}</span>";
		}

		$default  = $this->get_default();
		$attr_arr = array();
		foreach ( $this->attr as $name => $value ) {
			if ( 'value' !== $name ) {
				$attr_arr[] = "{$name}=\"{$value}\"";
			} else {
				$attr_arr[] = "{$name}=\"" . $default . '"';
			}
		}

		$attr = implode( ' ', $attr_arr );

		$checked = checked( $default, $this->get_value(), false );

		$html_array['content'] = "{$label}<input type=\"checkbox\" $attr $checked />\r\n{$description}";

		$this->html = implode( ' ', $html_array );

		return $this->html;
	}
}
