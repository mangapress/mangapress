<?php
/**
 * Select input builder
 *
 * @package MangaPress\Options\Fields\Types
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

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
        $is_grouped  = isset($options['pages']);
        $options_str = '';

        if ($is_grouped) {
            if (isset($options['no_val'])) {
                $options_str = '<option>' . esc_html($options['no_val']) . '</option>';
            }

            foreach ($options as $group => $option) {
                $options = '';
                if ($group == 'no_val') {
                    continue;
                }

                foreach ($option['pages'] as $option_val => $option_text) {
                    $selected = selected($value, $option_val, false);

                    $options .= vsprintf(
                        '<option value="%1$s" %2$s>%3$s</option>',
                        [
                            $option_val,
                            $selected,
                            $option_text,
                        ]
                    );
                }

                $options_str .= vsprintf(
                    '<optgroup label="%1$s">%2$s</optgroup>',
                    [
                        $option['title'],
                        $options,
                    ]
                );
            }
        } else {
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
        }

        $this->html = vsprintf(
            '<select %1$s>%2$s</select> %3$s',
            [
                $attr,
                $options_str,
                $this->get_description(),
            ]
        );

        return $this->html;
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
}
