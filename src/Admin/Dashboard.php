<?php
/**
 * Class Dashboard
 * @package MangaPress\Admin
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\Admin;

use MangaPress\PluginComponent;

/**
 * Class Dashboard
 * @package MangaPress\Admin
 */
class Dashboard implements PluginComponent
{

    /**
     * Initialize Dashboard widgets
     */
    public function init()
    {
        add_action('wp_dashboard_setup', [$this, 'dashboard_widgets']);
    }

    /**
     * Add dashboard widgets
     */
    public function dashboard_widgets()
    {
        wp_add_dashboard_widget(
            'mangapress_help_widget',
            __('Manga+Press Support', MP_DOMAIN),
            [$this, 'help_widget']
        );
    }

    public function help_widget()
    {
        //
    }
}
