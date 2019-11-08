<?php
/**
 * Textarea input field builder
 *
 * @package MangaPress\Options\Fields\Types
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\Options\Fields\Types;

use MangaPress\Options\Fields\Field;

/**
 * Class Textarea
 * @package MangaPress\Options\Fields\Types
 */
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
