<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 *
 * @todo Update screenshots
 * @todo Update PHPDoc comments
 */
/*
 Plugin Name: Manga+Press Comic Manager
 Plugin URI: http://www.manga-press.com/
 Description: Turns WordPress into a full-featured Webcomic Manager. Be sure to visit <a href="http://www.manga-press.com/">Manga+Press</a> for more info.
 Version: 2.8-alpha
 Author: Jessica Green
 Author URI: http://www.jes.gs
*/
/*
 * (c) 2013 Jessica C Green
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
    define('MP_VERSION', '2.8-alpha');

if (!defined('MP_DB_VERSION'))
    define('MP_DB_VERSION', '1.1');

if (!defined('MP_FOLDER'))
    define('MP_FOLDER', $plugin_folder);

if (!defined('MP_ABSPATH'))
    define('MP_ABSPATH', plugin_dir_path(__FILE__));

if (!defined('MP_URLPATH'))
    define('MP_URLPATH', plugin_dir_url(__FILE__));

if (!defined('MP_LANG'))
    define('MP_LANG', $plugin_folder . '/lang');

if (!defined('MP_DOMAIN'))
    define('MP_DOMAIN', $plugin_folder);

include_once('framework/FrameworkHelper.php');
include_once('framework/PostType.php');
include_once('framework/Taxonomy.php');
include_once('framework/View.php');
include_once('framework/Options.php');
include_once('framework/Form/Element.php');

include_once('includes/functions.php');
include_once('includes/template-functions.php');
include_once('comic-post-type.php');
include_once('mangapress-install.php');
include_once('mangapress-posts.php');
include_once('mangapress-options.php');

register_activation_hook(__FILE__, array('MangaPress_Install', 'do_activate'));
register_deactivation_hook( __FILE__, array('MangaPress_Install', 'do_deactivate'));

add_action('init', array('MangaPress_Bootstrap', 'init'));
add_action('setup_theme', array('MangaPress_Bootstrap', 'setup_theme'));

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
    protected static $_options;

    /**
     * Setting data class
     *
     * @var MangaPress_Settings
     */
    protected static $_options_data;

    /**
     * Instance of MangaPress_Bootstrap
     *
     * @var MangaPress_Bootstrap
     */
    protected static $_instance;

    /**
     * MangaPress Posts object
     *
     * @var \MangaPress_Posts
     */
    protected $_posts;

    /**
     * Static function used to initialize Bootstrap
     *
     * @return void
     */
    public static function init()
    {
        global $mp;

        self::set_options();

        load_plugin_textdomain(MP_DOMAIN, false, MP_LANG);

        self::$_instance  = new self();
        $mp->_posts       = new MangaPress_Posts();
        self::$_options_data = new MangaPress_Settings();
    }

    /**
     * Because register_theme_directory() can't run on init.
     *
     * @return void
     */
    public static function setup_theme()
    {
        register_theme_directory('plugins/' . MP_FOLDER . '/themes');
    }

    /**
     * PHP5 constructor method
     *
     * @return void
     */
    protected function __construct()
    {

        $mp_options = $this->get_options();

        /*
         * Navigation style
         */
        wp_register_style('mangapress-nav', MP_URLPATH . 'css/nav.css', null, MP_VERSION, 'screen');

        add_action('template_include', 'mpp_comic_single_page');

        /*
         * Disable/Enable Default Navigation CSS
         */
        if ($mp_options['nav']['nav_css'] == 'default_css')
            add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));

        /*
         * Comic Navigation
         */
        if ($mp_options['nav']['insert_nav'])
            add_action('the_content', 'mpp_comic_insert_navigation');

        /*
         * Lastest Comic Page
         */
        if ((bool)$mp_options['basic']['latestcomic_page']
                && !(bool)$mp_options['basic']['latestcomic_page_template']) {
            add_filter('the_content', 'mpp_filter_latest_comic');
        }

        /*
         * Latest Comic Page template override
         */
        if ((bool)$mp_options['basic']['latestcomic_page_template']) {
            add_filter('template_include', 'mpp_latest_comic_page');
        }
        /*
         * Comic Archive Page
         */
        if ((bool)$mp_options['basic']['comicarchive_page']
                && !(bool)$mp_options['basic']['comicarchive_page_template']) {
            add_filter('the_content', 'mpp_filter_comic_archivepage');
        }

        /*
         * Comic Archive Page template override
         */
        if ((bool)$mp_options['basic']['comicarchive_page_template'])
            add_filter('template_include', 'mpp_comic_archivepage');

        /*
         * Comic Thumbnail Banner
         */
        add_image_size (
            'comic-banner',
            $mp_options['comic_page']['banner_width'],
            $mp_options['comic_page']['banner_height'],
            true
        );

        /*
         * Comic Thumbnail size for Comics Listing screen
         */
        add_image_size('comic-admin-thumb', 60, 80, true);

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

        if (get_option('mangapress_upgrade') == 'yes') {
            MangaPress_Install::do_upgrade();
        }

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
        self::$_options = maybe_unserialize(get_option('mangapress_options'));
    }

    /**
     * Get MangaPress options
     *
     * @return array
     */
    public function get_options()
    {
        return self::$_options;
    }

    /**
     * Get options data class
     *
     * @return MangaPress_Settings
     */
    public static function get_options_data()
    {
        return self::$_options_data;
    }

    /**
     * Get instance of MangaPress_Bootstrap
     *
     * @return MangaPress_Bootstrap
     */
    public static function get_instance()
    {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Enqueue default navigation stylesheet
     *
     * @return void
     */
    public function wp_enqueue_scripts()
    {
        wp_enqueue_style('mangapress-nav');
    }

}
