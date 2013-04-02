<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */

/**
 * MangaPress_Textarea
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress_Textarea
 * @version $Id$
 */
class MangaPress_Textarea extends MangaPress_Element
{

    /**
     * Echo form element
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->_html;
    }

}