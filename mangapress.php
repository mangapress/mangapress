<?php
/**
 * @package MangaPress
 * @version $Id$
 * @author  Jessica Green <support@manga-press.com>
 */
//phpcs:disable
/*
 Plugin Name: Manga+Press Comic Manager
 Plugin URI: http://www.manga-press.com/
 Description: Turns WordPress into a full-featured Webcomic Manager. Be sure to visit <a href="http://www.manga-press.com/">Manga+Press</a> for more info.
 Version: 4.0.0-rc.2
 Author: Jess Green
 Author URI: http://www.jessgreen.io
 Text Domain: mangapress
 Domain Path: /languages
*/

//phpcs:enable

use MangaPress\Bootstrap;
use MangaPress\Install;

if (!class_exists('WP')) {
    die('No access allowed!');
}

require_once __DIR__ . '/vendor/autoload.php';

if (!defined('MP_VERSION')) {
    define('MP_VERSION', '4.0.0-rc.2');
}

if (!defined('MP_DOMAIN')) {
    define('MP_DOMAIN', 'mangapress');
}

if (!defined('MP_BASENAME')) {
    define('MP_BASENAME', plugin_basename(__FILE__));
}

if (!defined('MP_ABSPATH')) {
    define('MP_ABSPATH', trailingslashit(dirname(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . MP_BASENAME)));
}

if (!defined('MP_URLPATH')) {
    define('MP_URLPATH', plugin_dir_url(__FILE__));
}

$install = Install::get_instance();

register_activation_hook(__FILE__, [$install, 'do_activate']);
register_deactivation_hook(__FILE__, [$install, 'do_deactivate']);

(new Bootstrap())->init();
