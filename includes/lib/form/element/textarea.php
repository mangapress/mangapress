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
 * MangaPress_Textarea
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress_Textarea
 * @version $Id$
 */
class Textarea extends Element
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