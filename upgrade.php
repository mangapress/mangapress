<?php
/**
 * Run functionality for upgrades
 *
 * @package MangaPress
 * @subpackage Upgrade
 * @version $Id$
 * @author Jess Green <jgreen at psy-dreamer.com>
 */
if ( !class_exists('WP') || !get_option('mangapress_upgrade') ) {
    exit();
}

update_option('mangapress_ver', MP_VERSION);
delete_option( 'mangapress_upgrade' );

flush_rewrite_rules(false);