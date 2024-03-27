<?php
/**
 * MangaPress plugin starter file
 *
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */

/*
Plugin Name: Manga+Press Comic Manager
Plugin URI: http://www.manga-press.com/
Description: Turns WordPress into a full-featured Webcomic Manager. Be sure to visit <a href="http://www.manga-press.com/">Manga+Press</a> for more info.
Version: 3.0.3
Requires PHP: 7.4
Requires at least: 6.4
Author: Jess Green
Author URI: http://www.jesgs.com
Text Domain: mangapress
Domain Path: /languages
*/

/*
 * (c) 2024 Jessica C Green
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
$mangapress_plugin_folder = plugin_basename( __DIR__ );

if ( ! defined( 'MP_VERSION' ) ) {
	define( 'MP_VERSION', '3.0.3' );
}

if ( ! defined( 'MP_FOLDER' ) ) {
	define( 'MP_FOLDER', $mangapress_plugin_folder );
}

if ( ! defined( 'MP_ABSPATH' ) ) {
	define( 'MP_ABSPATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'MP_URLPATH' ) ) {
	define( 'MP_URLPATH', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'MP_LANG' ) ) {
	define( 'MP_LANG', $mangapress_plugin_folder . '/languages' );
}

require_once MP_ABSPATH . 'includes/lib/form/class-mangapress-element.php';
require_once MP_ABSPATH . 'includes/lib/class-mangapress-contenttype.php';
require_once MP_ABSPATH . 'includes/lib/class-mangapress-posttype.php';
require_once MP_ABSPATH . 'includes/lib/class-mangapress-taxonomy.php';
require_once MP_ABSPATH . 'includes/lib/class-mangapress-widget-calendar.php';
require_once MP_ABSPATH . 'includes/functions.php';
require_once MP_ABSPATH . 'includes/deprecated-functions.php';
require_once MP_ABSPATH . 'includes/template-functions.php';
require_once MP_ABSPATH . 'class-mangapress-install.php';
require_once MP_ABSPATH . 'class-mangapress-admin.php';
require_once MP_ABSPATH . 'class-mangapress-options.php';
require_once MP_ABSPATH . 'class-mangapress-posts.php';
require_once MP_ABSPATH . 'class-mangapress-bootstrap.php';

$mangapress_install = MangaPress_Install::get_instance();

register_activation_hook( __FILE__, array( $mangapress_install, 'do_activate' ) );
register_deactivation_hook( __FILE__, array( $mangapress_install, 'do_deactivate' ) );

add_action( 'plugins_loaded', array( 'MangaPress_Bootstrap', 'load_plugin' ) );
