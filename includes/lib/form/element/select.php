<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */

/**
 * MangaPress_Select
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress_Select
 * @version $Id$
 */
class MangaPress_Select extends MangaPress_Element
{
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array();

    /**
     * Echo form element
     *
     * @return string
     */
    public function __toString()
    {
        $options = $this->get_default();
        $attr_arr = array();
        foreach ($this->_attr as $name => $value) {
            if ($name != 'value')
                $attr_arr[] = "{$name}=\"{$value}\"";
        }

        $attr = implode(" ", $attr_arr);


        $value = $this->get_value();
        $options_str = "";
        foreach ($options as $option_val => $option_text) {
            $selected = selected($value, $option_val, false);
            $options_str .= "<option value=\"$option_val\" $selected>{$option_text}</option>";
        }

        $this->_html = "<select $attr>\n$options_str</select>";

        return $this->_html;
    }

    /**
     * Set default values
     *
     * @param array $values
     * @return \MangaPress_Select
     */
    public function set_default($values)
    {
        foreach ($values as $key => $value) {
            $this->_options[$key] = $value;
        }

        return $this;
    }

    /**
     * Get default values
     *
     * @return array
     */
    public function get_default()
    {
        return $this->_options;
    }
}