<?php
/**
 * Admin
 * @package MangaPress
 */

namespace MangaPress\Admin;

use MangaPress\PluginComponent;
use MangaPress\Options\OptionsGroup;
use function MangaPress\Admin\Functions\get_current_tab;
use function MangaPress\Admin\Functions\options_tabs;

/**
 * Class Admin
 * @package MangaPress\Admin
 */
class Admin implements PluginComponent
{
    /**
     * @var OptionsGroup $options_group
     */
    protected $options_group;

    /**
     * Page slug constant
     *
     * @var string
     */
    const ADMIN_PAGE_SLUG = 'mangapress-options-page';

    public function __construct()
    {
        $this->options_group = OptionsGroup::get_instance();
    }

    /**
     * Constructor method
     *
     * @return void
     */
    public function init()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Load our admin page
     *
     * @return void
     */
    public function admin_menu()
    {
        global $mangapress_page_hook;

        $mangapress_page_hook = add_options_page(
            __('Manga+Press Options', MP_DOMAIN),
            __('Manga+Press Options', MP_DOMAIN),
            'manage_options',
            self::ADMIN_PAGE_SLUG,
            [$this, 'load_page']
        );

        add_action("load-{$mangapress_page_hook}", [$this, 'load_help_tabs']);
    }


    /**
     * Load the admin page
     *
     * @return void
     */
    public function load_page()
    {
        include_once MP_ABSPATH . '/resources/admin/pages/options.php';
    }


    /**
     * Load contextual help tabs
     *
     * @return void
     */
    public function load_help_tabs()
    {
        $screen = get_current_screen();

        $tab = get_current_tab();
        $screen->add_help_tab($this->get_help_tabs($tab));
    }


    /**
     * Get help tab data for current option tab
     *
     * @param string $option_tab
     * @return array
     */
    public function get_help_tabs($option_tab)
    {
        $help_tabs = [
            'basic'      => [
                'id'      => 'help_basic',
                'title'   => __('Basic Options Help'),
                'content' => $this->get_help_tab_contents(),
            ],
            'comic_page' => [
                'id'      => 'help_comic_page',
                'title'   => __('Comic Page Options Help'),
                'content' => $this->get_help_tab_contents('comic_page'),
            ],
            'nav'        => [
                'id'      => 'help_nav',
                'title'   => __('Navigation Options Help'),
                'content' => $this->get_help_tab_contents('nav'),
            ],
        ];

        return $help_tabs[$option_tab];
    }


    /**
     * Get help tab contents from file
     *
     * @param string $help_tab Name of tab content to get
     * @return string
     */
    public function get_help_tab_contents($help_tab = 'basic')
    {
        ob_start();
        switch ($help_tab) {
            case 'basic':
                include_once MP_ABSPATH . '/resources/admin/pages/help-basic.php';
                break;
            case 'comic_page':
                include_once MP_ABSPATH . '/resources/admin/pages/help-comic-page.php';
                break;
            case 'nav':
                include_once MP_ABSPATH . '/resources/admin/pages/help-nav.php';
                break;
            default:
                // have a default response
        }

        return ob_get_clean();
    }


    /**
     * Display options tabs
     */
    public function options_page_tabs()
    {
        $current = get_current_tab();
        $tabs    = options_tabs();
        $links   = [];
        foreach ($tabs as $tab => $tab_data) {
            $admin_link = add_query_arg(
                [
                    'page' => 'mangapress-options-page',
                    'tab'  => $tab,
                ],
                admin_url('options-general.php')
            );

            $classes = $tab === $current ? 'nav-tab nav-tab-active' : 'nav-tab';

            $links[] = vsprintf(
                '<a class="%1$s" href="%2$s">%3$s</a>',
                [
                    $classes,
                    $admin_link,
                    $tab_data['title'],
                ]
            );
        }

        echo '<h2 class="nav-tab-wrapper">';

        foreach ($links as $link) {
            echo $link;
        }

        echo '</h2>';
    }

    /**
     * Enqueue scripts for admin
     *
     * @param string $hook
     * @global string $mangapress_page_hook
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
