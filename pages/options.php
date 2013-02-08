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
class View_OptionsPage extends View
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

    public function get_settings_page_tabs()
    {
         $tabs = array(
            'basic'      => 'Basic Manga+Press Options',
            'comic_page' => 'Comic Page Options',
            'nav'        => 'Navigation Options',
         );

         return $tabs;
    }

    public function options_page_tabs($current = 'basic')
    {
        if ( isset ( $_GET['tab'] ) ) {
            $current = $_GET['tab'];
        } else {
            $current = 'basic';
        }

        $tabs = $this->get_settings_page_tabs();
        $links = array();
        foreach( $tabs as $tab => $name ){
            if ( $tab == $current ){
                $links[] = "<a class=\"nav-tab nav-tab-active\" href=\"?page=mangapress-options-page&tab={$tab}\">{$name}</a>";
            } else {
                $links[] = "<a class=\"nav-tab\" href=\"?page=mangapress-options-page&tab={$tab}\">{$name}</a>";
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
        $tabs = array_keys($this->get_settings_page_tabs());

        if (isset($_GET['tab']) && in_array($_GET['tab'], $tabs)) {
            return $_GET['tab'];
        } else {
            return 'basic';
        }
    }

}
