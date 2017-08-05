<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */
namespace MangaPress\Form\Element;

use MangaPress\Form\Element;

/**
 * MangaPress_Checkbox
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress_Checkbox
 * @version $Id$
 */
class Checkbox extends Element
{
    /**
     * Checked value — saved to DB
     * @var boolean
     */
    protected $checked;


    /**
     * Get checked value
     * @return mixed
     */
    public function get_checked()
    {
        return $this->checked;
    }


    /**
     * Set checked value
     * @param mixed $checked
     * @return \MangaPress\Form\Element\Checkbox
     */
    public function set_checked($checked)
    {
        $this->checked = $checked;
        return $this;
    }


    /**
     * Display form element
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
        if ($desc) {
            $description = "<span class=\"description\">{$desc}</span>";
        }

        $default = $this->get_default();
        $attr_arr = array();
        foreach ($this->attr as $name => $value) {
            $attr_arr[] = "{$name}=\"{$value}\"";
        }

        $attr = implode(" ", $attr_arr);

        $checked = checked($this->get_checked(), $this->get_value(), false);

        $name = $this->get_name();
        $hidden = "<input type=\"hidden\" name=\"{$name}\" value=\"{$default}\" />";
        $htmlArray['content'] = "{$hidden}{$label}<input type=\"checkbox\" $attr $checked />\r\n{$description}";

        $this->html = implode(' ', $htmlArray);

        return $this->html;

    }
}
