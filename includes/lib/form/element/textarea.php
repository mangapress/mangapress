<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <support@manga-press.com>
 * @package MangaPress
 */
namespace MangaPress\Lib\Form\Element;
use MangaPress\Lib\Form\Element as Element;

/**
 * Textarea
 *
 * @author Jess Green <support@manga-press.com>
 * @package Textarea
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
        return $this->_html;
    }

}