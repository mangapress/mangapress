<?php
/**
 * @package    Manga_Press
 * @subpackage Uninstall
 * @version    $Id$
 * @author     Jess Green <support@manga-press.com>
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    die('You are not allowed to call this page directly.');
}

if(!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}

//
// delete comic options from database
delete_option('mangapress_options');
delete_option('mangapress_ver');
delete_option('mangapress_db_ver');
delete_option('mangapress_default_category');

flush_rewrite_rules();