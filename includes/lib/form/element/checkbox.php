<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */
namespace MangaPress\Lib\Form\Element;
use MangaPress\Lib\Form\Element as Element;

/**
 * Checkbox
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package Checkbox
 * @version $Id$
 */
class Checkbox extends Element
{
    /**
     * Display form element
     *
     * @return string
     */
    public function __toString()
    {
        $label = '';
        if (!empty($this->_label)) {
            $id = $this->get_attributes('id');
            $class = " class=\"label-$id\"";
            $label = "<label for=\"$id\"$class>$this->_label</label>\r\n";
        }

        $desc = $this->get_description();
        if ($desc) {
            $description = "<span class=\"description\">{$desc}</span>";
        }

        $default = $this->get_default();
        $attr_arr = array();
        foreach ($this->_attr as $name => $value) {
            if ($name != 'value') {
                $attr_arr[] = "{$name}=\"{$value}\"";
            } else {
                $attr_arr[] = "{$name}=\"" . $default . "\"";
            }
        }

        $attr = implode(" ", $attr_arr);

        $checked = checked($default, $this->get_value(), false);

        $htmlArray['content'] = "{$label}<input type=\"checkbox\" $attr $checked />\r\n{$description}";

        $this->_html = implode(' ', $htmlArray);

        return $this->_html;

    }
}
