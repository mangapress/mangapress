<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author  Jessica Green <support@manga-press.com>
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
namespace MangaPress;
/*
 * (c) 2008-2019 Jessica C Green
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
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    die('You are not allowed to call this page directly.');
}

$plugin_folder = plugin_basename(dirname(__FILE__));

if (!defined('MP_VERSION')) {
    define('MP_VERSION', '4.0.0'); // @todo replace with a call to get_plugin_data
}

if (!defined('MP_FOLDER')) {
    define('MP_FOLDER', $plugin_folder);
}

if (!defined('MP_ABSPATH')) {
    define('MP_ABSPATH', dirname(__FILE__) . '/');
}

if (!defined('MP_URLPATH')) {
    define('MP_URLPATH', plugin_dir_url(__FILE__));
}

if (!defined('MP_LANG')) {
    define('MP_LANG', $plugin_folder . '/languages');
}

if (!defined('MP_DOMAIN')) {
    define('MP_DOMAIN', 'mangapress');
}

require_once MP_ABSPATH . 'includes/lib/form/class-element.php';
require_once MP_ABSPATH . 'includes/lib/class-flash-messages.php';
require_once MP_ABSPATH . 'includes/lib/class-content-type.php';
require_once MP_ABSPATH . 'includes/lib/class-post-type.php';
require_once MP_ABSPATH . 'includes/lib/class-taxonomy.php';
require_once MP_ABSPATH . 'includes/lib/class-mp-calendar-widget.php';
require_once MP_ABSPATH . 'includes/general-functions.php';
require_once MP_ABSPATH . 'includes/theme-functions.php';
require_once MP_ABSPATH . 'includes/template-functions.php';
require_once MP_ABSPATH . 'includes/deprecated.php';
require_once MP_ABSPATH . 'mangapress-install.php';
require_once MP_ABSPATH . 'mangapress-admin.php';
require_once MP_ABSPATH . 'mangapress-options.php';
require_once MP_ABSPATH . 'mangapress-posts.php';
require_once MP_ABSPATH . 'mangapress-bootstrap.php';

$install = Install::get_instance();

register_activation_hook(__FILE__, [$install, 'do_activate']);
register_deactivation_hook(__FILE__, [$install, 'do_deactivate']);

add_action('plugins_loaded', [ 'MangaPress\Bootstrap', 'load_plugin']);
