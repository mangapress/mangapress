<?php
/**
 * MangaPress
 *
 * @package Admin
 * @author  Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */

namespace MangaPress;

/**
 * Admin
 * Class that handles Manga+Press' admin page configuration
 *
 * @author Jess Green <jgreen at psy-dreamer.com>
 */
class Admin
{
    /**
     * Page slug constant
     *
     * @var string
     */
    const ADMIN_PAGE_SLUG = 'mangapress-options-page';

    /**
     * Constructor method
     *
     * @return void
     */
    public static function init()
    {
        add_action('admin_menu', ['\MangaPress\Admin', 'admin_menu']);
        add_action('admin_enqueue_scripts', ['\MangaPress\Admin', 'enqueue_scripts']);
    }

    /**
     * Load our admin page
     *
     * @return void
     */
    public static function admin_menu()
    {
        global $mangapress_page_hook;

        $mangapress_page_hook = add_options_page(
            __("Manga+Press Options", MP_DOMAIN),
            __("Manga+Press Options", MP_DOMAIN),
            'manage_options',
            self::ADMIN_PAGE_SLUG,
            ['\MangaPress\Admin', 'load_page']
        );

        add_action("load-{$mangapress_page_hook}", ['\MangaPress\Admin', 'load_help_tabs']);
    }


    /**
     * Load the admin page
     *
     * @return void
     */
    public static function load_page()
    {
        include_once MP_ABSPATH . '/includes/pages/options.php';
    }


    /**
     * Load contextual help tabs
     *
     * @return void
     */
    public static function load_help_tabs()
    {
        $screen = get_current_screen();

        $tab = self::get_current_tab();
        $screen->add_help_tab(self::get_help_tabs($tab));
    }


    /**
     * Get help tab data for current option tab
     *
     * @param  string $option_tab
     * @return array
     */
    public static function get_help_tabs($option_tab)
    {
        $help_tabs = [
            'basic'      => [
                'id'      => 'help_basic',
                'title'   => __('Basic Options Help'),
                'content' => self::get_help_tab_contents(),
            ],
            'comic_page' => [
                'id'      => 'help_comic_page',
                'title'   => __('Comic Page Options Help'),
                'content' => self::get_help_tab_contents('comic_page'),
            ],
            'nav'        => [
                'id'      => 'help_nav',
                'title'   => __('Navigation Options Help'),
                'content' => self::get_help_tab_contents('nav'),
            ],
        ];

        return $help_tabs[$option_tab];
    }


    /**
     * Get help tab contents from file
     *
     * @param  string $help_tab Name of tab content to get
     * @return string
     */
    public static function get_help_tab_contents($help_tab = 'basic')
    {
        ob_start();
        switch ($help_tab) {
        case 'basic' :
            include_once MP_ABSPATH . '/includes/pages/help-basic.php';
            break;
        case 'comic_page':
            include_once MP_ABSPATH . '/includes/pages/help-comic-page.php';
            break;
        case 'nav':
            include_once MP_ABSPATH . '/includes/pages/help-nav.php';
            break;
        default:
            // have a default response
        }

        return ob_get_clean();
    }


    /**
     * Display options tabs
     *
     * @param  string $current Current tab
     * @return void
     */
    public static function options_page_tabs($current = 'basic')
    {
        $current = filter_input(INPUT_GET, 'tab')
            ? filter_input(INPUT_GET, 'tab') : 'basic';

        $tabs = Options::options_sections();

        $links = [];
        foreach ($tabs as $tab => $tab_data) {
            if ($tab == $current) {
                $links[] = "<a class=\"nav-tab nav-tab-active\" href=\"?page=mangapress-options-page&tab={$tab}\">{$tab_data['title']}</a>";
            } else {
                $links[] = "<a class=\"nav-tab\" href=\"?page=mangapress-options-page&tab={$tab}\">{$tab_data['title']}</a>";
            }
        }

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
    public static function get_current_tab()
    {
        $tabs = Options::get_options_sections();

        $current_tab = filter_input(INPUT_GET, 'tab');
        if (in_array($current_tab, $tabs)) {
            return $current_tab;
        } else {
            return 'basic';
        }
    }


    /**
     * Enqueue scripts for admin
     *
     * @param  string $hook
     * @global string $mangapress_page_hook
     */
    public static function enqueue_scripts($hook)
    {
        global $mangapress_page_hook;

        if ($hook == $mangapress_page_hook) {
            wp_enqueue_script('mangapress-syntax-highlighter-cssbrush');
            wp_enqueue_style('mangapress-syntax-highlighter-css');
        }
    }
}
