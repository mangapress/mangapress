<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 *
 */
/*
 Plugin Name: Manga+Press Comic Manager
 Plugin URI: http://www.manga-press.com/
 Description: Turns WordPress into a full-featured Webcomic Manager. Be sure to visit <a href="http://www.manga-press.com/">Manga+Press</a> for more info.
 Version: 3.5.0
 Author: Jess Green
 Author URI: http://www.jessgreen.io
 Text Domain: mangapress
 Domain Path: /languages
*/
/*
 * (c) 2017 Jessica C Green
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('You are not allowed to call this page directly.');

$plugin_folder = plugin_basename(dirname(__FILE__));

if (!defined('MP_VERSION'))
    define('MP_VERSION', '3.5.0');

if (!defined('MP_FOLDER'))
    define('MP_FOLDER', $plugin_folder);

if (!defined('MP_ABSPATH'))
    define('MP_ABSPATH', dirname(__FILE__) . '/');

if (!defined('MP_URLPATH'))
    define('MP_URLPATH', plugin_dir_url(__FILE__));

if (!defined('MP_LANG'))
    define('MP_LANG', $plugin_folder . '/languages');

if (!defined('MP_DOMAIN'))
    define('MP_DOMAIN', 'mangapress');

require_once MP_ABSPATH . 'includes/lib/form/class-element.php';
require_once MP_ABSPATH . 'includes/lib/class-flash-messages.php';
require_once MP_ABSPATH . 'includes/lib/class-content-type.php';
require_once MP_ABSPATH . 'includes/lib/class-post-type.php';
require_once MP_ABSPATH . 'includes/lib/class-taxonomy.php';
require_once MP_ABSPATH . 'includes/lib/class-mp-calendar-widget.php';
require_once MP_ABSPATH . 'includes/functions.php';
require_once MP_ABSPATH . 'includes/theme-functions.php';
require_once MP_ABSPATH . 'includes/template-functions.php';
require_once MP_ABSPATH . 'mangapress-install.php';
require_once MP_ABSPATH . 'mangapress-admin.php';
require_once MP_ABSPATH . 'mangapress-options.php';
require_once MP_ABSPATH . 'mangapress-posts.php';

$install = MangaPress_Install::get_instance();

register_activation_hook(__FILE__, array($install, 'do_activate'));
register_deactivation_hook(__FILE__, array($install, 'do_deactivate'));

add_action('plugins_loaded', array('MangaPress_Bootstrap', 'load_plugin'));

/**
 * Plugin bootstrap class.
 *
 * @package MangaPress
 * @subpackage MangaPress_Bootstrap
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class MangaPress_Bootstrap
{


    /**
     * Options array
     *
     * @var array
     */
    protected static $options;


    /**
     * MangaPress Posts object
     *
     * @var \MangaPress_Posts
     */
    protected static $posts_helper;


    /**
     * Options helper object
     *
     * @var \MangaPress_Options
     */
    protected static $options_helper;


    /**
     * Admin page helper
     *
     * @var MangaPress_Admin
     */
    protected static $admin_helper;


    /**
     * Flash Message helper
     *
     * @var MangaPress_FlashMessages
     */
    protected static $flashmessage_helper;


    /**
     * Static function used to initialize Bootstrap
     *
     * @return void
     */
    public static function load_plugin()
    {
        load_plugin_textdomain(MP_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages');

        add_action('init', array(__CLASS__, 'init'), 500);
        add_action('widgets_init', array(__CLASS__, 'widgets_init'));
    }


    /**
     * Run init functionality
     *
     * @see init() hook
     * @return void
     */
    public static function init()
    {
        self::set_options();

        self::$posts_helper   = new MangaPress_Posts();
        self::$admin_helper   = new MangaPress_Admin();
        self::$options_helper = new MangaPress_Options();
        self::$flashmessage_helper = new MangaPress_FlashMessages(array(
            'transient_name' => 'mangapress_messages'
        ));


        self::load_current_options();

        add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_scripts'));

        add_filter('template_include', 'mangapress_template_loader');

        if (get_option('mangapress_upgrade') == 'yes') {
            MangaPress_Install::get_instance()->do_upgrade();
        }
    }


    /**
     * Register widgets
     */
    public static function widgets_init()
    {
        register_widget('MangaPress_Widget_Calendar');
    }


    /**
     * Get a MangaPress helper
     *
     * @param string $helper_name Allowed values: admin, options, posts, flashmessages
     * @return \MangaPress_Admin|\MangaPress_Options|\MangaPress_Posts|\MangaPress_FlashMessages|\WP_Error
     */
    public static function get_helper($helper_name)
    {
        $helper = "{$helper_name}_helper";
        if (property_exists(__CLASS__, $helper)) {
            return self::${$helper};
        }

        return new WP_Error('_mangapress_helper_access', 'No helper exists by that name');
    }


    /**
     * Set MangaPress options. This method should run every time
     * MangaPress options are updated.
     *
     * @uses init()
     * @see MangaPress_Bootstrap::init()
     *
     * @return void
     */
    public static function set_options()
    {
        self::$options = maybe_unserialize(get_option('mangapress_options'));
    }


    /**
     * Get MangaPress options
     *
     * @return array
     */
    public static function get_options()
    {
        return self::$options;
    }


    /**
     * Get one option from options array
     *
     * @param string $section Option section
     * @param string $option_name Option name
     * @return boolean|mixed
     */
    public static function get_option($section, $option_name)
    {
        if (!isset(self::$options[$section][$option_name])) {
            return false;
        }

        return self::$options[$section][$option_name];
    }


    /**
     * Load current plugin options
     *
     * @return void
     */
    private static function load_current_options()
    {
        $mp_options = self::get_options();

        /*
         * Disable/Enable Default Navigation CSS
         */
        if ($mp_options['nav']['nav_css'] == 'default_css') {
            add_action('wp_enqueue_scripts', array(__CLASS__, 'wp_enqueue_scripts'));
        }

        /*
         * Comic Page size
         */
        if ($mp_options['comic_page']['generate_comic_page']){
            add_image_size(
                'comic-page',
                $mp_options['comic_page']['comic_page_width'],
                $mp_options['comic_page']['comic_page_height'],
                false
            );
        }

        /*
         * Comic Thumbnail size for Comics Listing screen
         */
        add_image_size('comic-admin-thumb', 60, 80, true);

    }


    /**
     * Enqueue default navigation stylesheet
     *
     * @return void
     */
    public static function wp_enqueue_scripts()
    {
        /*
         * Navigation style
         */
        wp_register_style(
            'mangapress-nav',
            MP_URLPATH . 'assets/css/nav.css',
            null,
            MP_VERSION,
            'screen'
        );

        wp_enqueue_style('mangapress-nav');
    }


    /**
     * Enqueue admin-related styles
     *
     * @return void
     */
    public static function admin_enqueue_scripts()
    {
        wp_enqueue_style(
            'mangapress-icons',
            plugins_url('assets/css/font.css', __FILE__),
            null,
            MP_VERSION,
            'screen'
        );
    }
}
