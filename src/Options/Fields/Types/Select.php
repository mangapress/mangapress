<?php


namespace MangaPress\Options\Fields\Types;

use MangaPress\Options\Fields\Field;

/**
 * Class Select
 * @package MangaPress\Options\Fields\Types
 */
class Select extends Field
{
    /**
     * Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Echo form element
     *
     * @return string
     */
    public function __toString()
    {
        $options  = $this->get_default();
        $attr_arr = [];
        foreach ($this->attr as $name => $value) {
            if ($name !== 'value') {
                $attr_arr[] = "{$name}=\"{$value}\"";
            }
        }

        $attr = implode(" ", $attr_arr);

        $value       = $this->get_value();
        $options_str = "";
        foreach ($options as $option_val => $option_text) {
            $selected    = selected($value, $option_val, false);
            $options_str .= vsprintf(
                '<option value="%1$s" %2$s>%3$s</option>',
                [
                    $option_val,
                    $selected,
                    $option_text,
                ]
            );
        }

        $this->html = vsprintf(
            '<select %1$s>%2$s</select> %3$s}',
            [
                $attr,
                $options_str,
                $this->get_description(),
            ]
        );

        return $this->html;
    }

    /**
     * Set default values
     *
     * @param array $values
     * @return Select
     */
    public function set_default($values)
    {
        foreach ($values as $key => $value) {
            $this->options[$key] = $value;
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
        return $this->options;
    }
}
