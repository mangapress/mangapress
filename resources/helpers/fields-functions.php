<?php
/**
 * Output additional fields
 * @package MangaPress\Helpers\Fields_Functions
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\Options\Fields\Functions;

/**
 * Output CSS display fields
 */
function navigation_css_display_cb()
{
    require MP_ABSPATH . '/resources/admin/pages/nav-css.php';
}
