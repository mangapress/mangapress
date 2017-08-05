<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */
require_once MP_ABSPATH . '/includes/lib/form/element/text.php';
/**
 * MangaPress_Number
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress_Text
 * @version $Id$
 */
class MangaPress_Number extends MangaPress_Text
{
    protected $type = 'number';


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

        $htmlArray['content'] = "{$label}<input type=\"number\" $attr />\r\n{$description}";

        $this->html = implode(' ', $htmlArray);

        return $this->html;
    }
}