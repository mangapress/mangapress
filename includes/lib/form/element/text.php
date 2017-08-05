<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */
namespace MangaPress\Form;
use MangaPress\Form\Element;

/**
 * MangaPress_Text
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress_Text
 * @version $Id$
 */
class Text extends Element
{

    /**
     * Echo form element
     *
     * @return string
     */
    public function __toString()
    {
        $label = '';
        if (!empty($this->label)) {
            $id = $this->get_attributes('id');
            $class = " class=\"label-$id\"";
            $label = "<label for=\"$id\"$class>$this->label</label>\r\n";
        }

        $desc = $this->get_description();
        $description = "";
        if ($desc) {
            $description = "<span class=\"description\">{$desc}</span>";
        }

        $attr = $this->build_attr_string();

        $htmlArray['content'] = "{$label}<input type=\"text\" $attr />\r\n{$description}";

        $this->html = implode(' ', $htmlArray);

        return $this->html;
    }
}