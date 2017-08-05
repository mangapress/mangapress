<?php
/**
 * @package MangaPress
 * @subpackage Uninstall
 * @version $Id$
 * @author Jess Green <jgreen at psy-dreamer.com>
 */
if( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();

//
// delete comic options from database
delete_option('mangapress_options');
delete_option('mangapress_ver');
delete_option('mangapress_db_ver');
delete_option('mangapress_default_category');

flush_rewrite_rules();