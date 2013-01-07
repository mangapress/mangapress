<?php

class Select extends Element
{
    public $options = array();

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

    public function set_default($values)
    {
        foreach ($values as $key => $value) {
            $this->options[$key] = $value;
        }

        return $this;
    }

    public function get_default()
    {
        return $this->options;
    }
}
?>