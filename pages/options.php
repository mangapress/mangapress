<?php
/**
 * @package MangaPress
 * @subpackage Pages
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */
/**
 * @package Pages
 * @subpackage Options
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class View_OptionsPage extends MangaPress_View
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $options = add_options_page(
            __("Manga+Press Options", MP_DOMAIN),
            __("Manga+Press Options", MP_DOMAIN),
            'manage_options',
            'mangapress-options-page',
            array($this, 'page')
        );

        $this->set_hook($options);
    }

    public function page()
    {
        include_once 'scripts/page.options.php';
    }

    /**
     * Display options tabs
     *
     * @param string $current Current tab
     * @return void
     */
    public function options_page_tabs($current = 'basic')
    {
        if ( isset ( $_GET['tab'] ) ) {
            $current = $_GET['tab'];
        } else {
            $current = 'basic';
        }

        $tabs = MangaPress_Bootstrap::get_options_data()->options_sections();

        $links = array();
        foreach( $tabs as $tab => $tab_data ){
            if ( $tab == $current ){
                $links[] = "<a class=\"nav-tab nav-tab-active\" href=\"?page=mangapress-options-page&tab={$tab}\">{$tab_data['title']}</a>";
            } else {
                $links[] = "<a class=\"nav-tab\" href=\"?page=mangapress-options-page&tab={$tab}\">{$tab_data['title']}</a>";
            };
        }

        echo get_screen_icon();
        echo '<h2 class="nav-tab-wrapper">';

        foreach ( $links as $link )
            echo $link;
        echo '</h2>';
    }

    public function get_current_tab()
    {
        $tabs = array_keys(MangaPress_Bootstrap::get_options_data()->options_sections());

        if (isset($_GET['tab']) && in_array($_GET['tab'], $tabs)) {
            return $_GET['tab'];
        } else {
            return 'basic';
        }
    }

}
