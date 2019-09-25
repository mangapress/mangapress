<?php


namespace MangaPress\Options\Fields\Types;

use MangaPress\Options\Fields\Field;

class Textarea extends Field
{
    /**
     * Echo form element
     *
     * @return string
     */
    public function __toString()
    {
        return $this->html;
    }
}
