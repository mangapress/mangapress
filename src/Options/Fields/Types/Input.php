<?php
/**
 * Generic Input class
 *
 * @package MangaPress\Options\Fields\Types
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\Options\Fields\Types;

use MangaPress\Options\Fields\Field;

/**
 * Class Input
 * @package MangaPress\Options\Fields\Types
 */
class Input extends Field
{
    protected $type;

    /**
     * Echo form element
     *
     * @return string
     */
    public function __toString()
    {
        $label = '';
        if (!empty($this->label)) {
            $id    = $this->get_attributes('id');
            $label = vsprintf(
                '<label for="$" class=\"label-$id\">$this->label</label>',
                [
                    $id,
                    $this->label,
                ]
            );
        }

        $attr = $this->build_attr_string();

        $htmlArray['content'] = vsprintf(
            '%1$s<input type="%4$s" %2$s /> %3$s',
            [
                $label,
                $attr,
                $this->get_description(),
                $this->type,
            ]
        );

        $this->html = implode(' ', $htmlArray);

        return $this->html;
    }
}
