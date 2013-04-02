<?php
/**
 * MangaPress
 * 
 * @package mangapress-admin
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */
/**
 * mangapress-admin
 *
 * @author Jess Green <jgreen at psy-dreamer.com>
 */
final class MangaPress_Admin
{
    /**
     * Page slug constant
     * 
     * @var string
     */
    const ADMIN_PAGE_SLUG = 'mangapress-options-page';
    
    /**
     * Instance of MangaPress_Options
     * 
     * @var MangaPress_Options
     */
    private $_options;
    
    /**
     * Constructor method
     * @return void
     */
    public function __construct()
    {        
        add_action('admin_menu', array($this, 'admin_menu'));
    }
    
    /**
     * Load our admin page
     * 
     * @return void
     */
    public function admin_menu()
    {
        $options = add_options_page(
            __("Manga+Press Options", MP_DOMAIN),
            __("Manga+Press Options", MP_DOMAIN),
            'manage_options',
            'mangapress-options-page',
            array($this, 'load_page')
        );        
    }
    
    /**
     * Load the admin page
     * 
     * @return void
     */
    public function load_page()
    {
        require_once MP_ABSPATH . '/includes/pages/options.php';
    }
    
    /**
     * Display options tabs
     *
     * @param string $current Current tab
     * @return void
     */
    public function options_page_tabs($current = 'basic')
    {        
        if (isset($_GET['tab'])) {
            $current = $_GET['tab'];
        } else {
            $current = 'basic';
        }

        $options = MangaPress_Bootstrap::get_instance()->get_helper('options');
        $tabs = $options->options_sections();

        $links = array();
        foreach($tabs as $tab => $tab_data) {
            if ($tab == $current) {
                $links[] = "<a class=\"nav-tab nav-tab-active\" href=\"?page=mangapress-options-page&tab={$tab}\">{$tab_data['title']}</a>";
            } else {
                $links[] = "<a class=\"nav-tab\" href=\"?page=mangapress-options-page&tab={$tab}\">{$tab_data['title']}</a>";
            }
        }

        echo get_screen_icon();
        echo '<h2 class="nav-tab-wrapper">';

        foreach ($links as $link) {
            echo $link;
        }
        
        echo '</h2>';
    }
    
    /**
     * Create options page tabs
     * 
     * @return string
     */
    public function get_current_tab()
    {
        $options = MangaPress_Bootstrap::get_instance()->get_helper('options');
        $tabs = $options->get_options_sections();

        if (isset($_GET['tab']) && in_array($_GET['tab'], $tabs)) {
            return $_GET['tab'];
        } else {
            return 'basic';
        }
    }
    
}
