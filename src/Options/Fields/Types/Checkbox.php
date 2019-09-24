<?php


namespace MangaPress\Options\Fields\Types;

use MangaPress\Options\Fields\Field;

/**
 * Class Checkbox
 * @package MangaPress\Options\Fields\Types
 */
class Checkbox extends Field
{
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
            $label = vsprintf(
                '<label for="%1$s" class="label-%1$s">%2$s</label>',
                [
                    $id,
                    $this->label
                ]
            ) . CRLF;
        }

        $desc = $this->get_description();
        $description = '';
        if ($desc) {
            $description = sprintf('<span class="description">%s</span>', $desc);
        }

        $default = $this->get_default();
        $attr_arr = array();
        foreach ($this->attr as $name => $value) {
            if ($name != 'value') {
                $attr_arr[] = "{$name}=\"{$value}\"";
            } else {
                $attr_arr[] = "{$name}=\"" . $default . "\"";
            }
        }

        $attr = implode(" ", $attr_arr);

        $checked = checked($default, $this->get_value(), false);

        $htmlArray['content'] = vsprint(
            '%1$s<input type="checkbox" %2$s %3$s /> %4$s',
            [
                $label,
                $attr,
                $checked,
                $description,
            ]
        );

        $this->html = implode(' ', $htmlArray);

        return $this->html;
    }
}
