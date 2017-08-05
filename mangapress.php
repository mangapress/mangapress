<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author Jess Green <jgreen at psy-dreamer.com>
 *
 */
/*
 Plugin Name: Manga+Press Comic Manager
 Plugin URI: http://www.manga-press.com/
 Description: Turns WordPress into a full-featured Webcomic Manager. Be sure to visit <a href="http://www.manga-press.com/">Manga+Press</a> for more info.
 Version: 4.0.0
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

if (!defined('MP_ABSPATH'))
    define('MP_ABSPATH', plugin_dir_path(__FILE__));

if (!defined('MP_URLPATH'))
    define('MP_URLPATH', plugin_dir_url(__FILE__));

/**
 * Autoload plugin's classes
 */
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'MangaPress\\';

    // base directory for the namespace prefix
    $base_dir = MP_ABSPATH . 'includes/lib/';
    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', strtolower($relative_class)) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

if (!defined('MP_VERSION'))
    define('MP_VERSION', '3.0.0');

if (!defined('MP_FOLDER'))
    define('MP_FOLDER', $plugin_folder);

if (!defined('MP_LANG'))
    define('MP_LANG', $plugin_folder . '/languages');

if (!defined('MP_DOMAIN'))
    define('MP_DOMAIN', 'mangapress');

require_once MP_ABSPATH . 'includes/functions.php';
require_once MP_ABSPATH . 'includes/template-functions.php';

$install = MangaPress\Plugin\Install::get_instance();

register_activation_hook(__FILE__, array($install, 'do_activate'));
register_deactivation_hook(__FILE__, array($install, 'do_deactivate'));

add_action('plugins_loaded', array('MangaPress\Plugin\Bootstrap', 'load_plugin'));
