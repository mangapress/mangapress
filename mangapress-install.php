<?php
/**
 * MangaPress Installation Class
 *
 * @package MangaPress
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * @subpackage MangaPress_Install
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
class MangaPress_Install
{
    /**
     * Current MangaPress DB version
     *
     * @var string
     */
    protected static $_version;

    /**
     * What type is the object? Activation, deactivation or upgrade?
     *
     * @var string
     */
    protected $_type;

    /**
     * Default options array
     *
     * @var array
     */
    protected static $_default_options =  array(
            'basic' => array(
                'order_by'                   => 'post_date',
                'group_comics'               => 0,
                'group_by_parent'            => 0,
                'latestcomic_page'           => 0,
                'comicarchive_page'          => 0,
                'latestcomic_page_template'  => 0,
                'comicarchive_page_template' => 0,				
            ),
            'comic_page' => array(
                'make_thumb'          => 0,
                'insert_banner'       => 0,
                'banner_width'        => 420,
                'banner_height'       => 100,
                'comic_post_count'    => 10,
                'generate_comic_page' => 0,
                'comic_page_width'    => 600,
                'comic_page_height'   => 1000,
            ),
            'nav' => array(
                'nav_css'    => 'custom_css',
                'insert_nav' => false,
            ),
        );


    /**
     * Static function for plugin activation.
     *
     * @return void
     */
    public static function do_activate()
    {
        global $wp_rewrite, $wp_version;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Check for capability
        if ( !current_user_can('activate_plugins') )
            wp_die( __('Sorry, you do not have suffient permissions to activate this plugin.', 'mangapress') );

        // Get the capabilities for the administrator
        $role = get_role('administrator');

        // Must have admin privileges in order to activate.
        if ( empty($role) )
            wp_die( __('Sorry, you must be an Administrator in order to use Manga+Press', 'mangapress') );

        $wp_rewrite->flush_rules();

        if ( version_compare ($wp_version, '3.0', '<=')) {
            wp_die(
                  'Sorry, only WordPress 3.0 and later are supported.'
                . ' Please upgrade to WordPress 3.0', 'Wrong Version'
            );
        }

        self::$_version = strval( get_option('mangapress_ver') );

        // version_compare will still evaluate against an empty string
        // so we have to tell it not to.
        if (version_compare(self::$_version, MP_VERSION, '<') && !(self::$_version == '')) {
                        
            add_option( 'mangapress_upgrade', 'yes', '', 'no');

        } elseif (self::$_version == '') {

            add_option( 'mangapress_ver', MP_VERSION, '', 'no');
            add_option( 'mangapress_options', serialize( self::$_default_options ), '', 'no' );

        }

    }

    /**
     * Static function for plugin deactivation.
     *
     * @return void
     */
    public static function do_deactivate()
    {
        global $wp_rewrite;

        $wp_rewrite->flush_rules();
    }

    /**
     * Static function for upgrade
     *
     * @return void
     */
    public static function do_upgrade()
    {
        $options = get_option('mangapress_options');

        // add new option to the array
        $options['basic']['group_by_parent'] = self::$_default_options['basic']['group_by_parent'];
        
        update_option( 'mangapress_options', $options);
        update_option('mangapress_ver', MP_VERSION);
        
        delete_option( 'mangapress_upgrade' );
    }
    
    /**
     * Returns default options
     * 
     * @return array
     */
    public static function get_default_options()
    {
        return self::$_default_options;
    }
}
