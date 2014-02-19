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
     * Constructor method
     * @return void
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Load our admin page
     *
     * @return void
     */
    public function admin_menu()
    {
        global $mangapress_page_hook;

        // Syntax highlighter
        wp_register_script(
            'mangapress-syntax-highlighter',
            MP_URLPATH . 'includes/pages/js/syntaxhighlighter/scripts/shCore.js'
        );

        // the brush we need...
        wp_register_script(
            'mangapress-syntax-highlighter-cssbrush',
            MP_URLPATH . 'includes/pages/js/syntaxhighlighter/scripts/shBrushCss.js',
            array('mangapress-syntax-highlighter')
        );

        // the style
        wp_register_style(
            'mangapress-syntax-highlighter-css',
            MP_URLPATH . 'includes/pages/js/syntaxhighlighter/styles/shCoreDefault.css'
        );

        $mangapress_page_hook = add_options_page(
            __("Manga+Press Options", MP_DOMAIN),
            __("Manga+Press Options", MP_DOMAIN),
            'manage_options',
            self::ADMIN_PAGE_SLUG,
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

    /**
     * Enqueue scripts for admin
     * 
     * @global string $mangapress_page_hook
     * @param string $hook
     */
    public function enqueue_scripts($hook)
    {
        global $mangapress_page_hook;

        if ($hook == $mangapress_page_hook) {
            wp_enqueue_script('mangapress-syntax-highlighter-cssbrush');
            wp_enqueue_style('mangapress-syntax-highlighter-css');
        }
    }
}
