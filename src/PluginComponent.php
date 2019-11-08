<?php
/**
 * @package MangaPress
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress;

/**
 * Interface PluginComponent
 * @package MangaPress
 */
interface PluginComponent
{
    /**
     * Run hooks on init hook
     */
    public function init();
}
