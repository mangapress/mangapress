<?php
/**
 * MangaPress
 * 
 * @package mangapress-options
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */
/**
 * mangapress-options
 *
 * @author Jess Green <jgreen at psy-dreamer.com>
 */
final class MangaPress_Options
{
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
        'permalink'  => array(
            ''
        ),
        'comic_page' => array(
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
    
    public function __construct()
    {
        ;
    }
    
    /**
     * Returns default options
     * Used by MangaPress_Install to handle defaults on activation
     *
     * @return array
     */
    public static function get_default_options()
    {
        return self::$_default_options;
    }
    
   /**
     * Helper function for creating default options fields.
     *
     * @param array $options Option fields array.
     * @return array
     */
    public function options_fields()
    {
        /*
         * Section
         *      |_ Option
         *              |_ Option Setting
         */
        $options = array(
            'basic' => array(
                'order_by' => array(
                    'id'    => 'order-by',
                    'title' => __('Order By', MP_DOMAIN),
                    'type'  => 'select',
                    'value' => array(
                        'post_date' => __('Date', MP_DOMAIN),
                        'post_id'   => __('Post ID', MP_DOMAIN),
                    ),
                    'valid'   => 'array',
                    'default' => 'post_date',
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'group_comics'      => array(
                    'id'    => 'group-comics',
                    'type'  => 'checkbox',
                    'title' => __('Group Comics', MP_DOMAIN),
                    'valid' => 'boolean',
                    'description' => __('Group comics by category. This option will ignore the parent category, and group according to the child-category.', MP_DOMAIN),
                    'default' => 0,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'group_by_parent'      => array(
                    'id'    => 'group-by-parent',
                    'type'  => 'checkbox',
                    'title' => __('Use Parent Category', MP_DOMAIN),
                    'valid' => 'boolean',
                    'description' => __('Group comics by top-most parent category. Use this option if you have sub-categories but want your navigation to function using the parent category.', MP_DOMAIN),
                    'default'     => 0,
                    'callback'    => array($this, 'settings_field_cb'),
                ),
                'latestcomic_page'  => array(
                    'id'    => 'latest-comic-page',
                    'type'  => 'select',
                    'title' => __('Latest Comic Page', MP_DOMAIN),
                    'value' => array(
                        'no_val' => __('Select a Page', MP_DOMAIN),
                    ),
                    'valid' => 'array',
                    'default'  => 0,
                    'callback' => array($this, 'ft_basic_page_dropdowns_cb'),
                ),
                'latestcomic_page_template' => array(  // New option in 3.0
                    'id'    => 'latestcomic-page-template',
                    'type'  => 'checkbox',
                    'title'       => __('Use Template', MP_DOMAIN),
                    'description' => __('Use theme template for Latest Comic Page.', MP_DOMAIN),
                    'valid'       => 'boolean',
                    'default'     => 0,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'comicarchive_page' => array(
                    'id'    => 'archive-page',
                    'type'  => 'select',
                    'title' => __('Comic Archive Page', MP_DOMAIN),
                    'value' => array(
                        'no_val' => __('Select a Page', MP_DOMAIN),
                    ),
                    'valid' => 'array',
                    'default' => 0,
                    'callback' => array($this, 'ft_basic_page_dropdowns_cb'),
                ),
                'comicarchive_page_template' => array(  // New option in 3.0
                    'id'    => 'comicarchive-page-template',
                    'type'  => 'checkbox',
                    'title'       => __('Use Template', MP_DOMAIN),
                    'description' => __('Use theme template for Comic Archive Page.', MP_DOMAIN),
                    'valid'       => 'boolean',
                    'default'     => 0,
                    'callback' => array($this, 'settings_field_cb'),
                ),
            ),
            'comic_page' => array(
                'comic_post_count'    =>  array(
                    'id'    => 'number-posts',
                    'type'  => 'text',
                    'title' => __('Comic Posts to Display', MP_DOMAIN),
                    'description' => __('Overrides values set in Reading Settings.', MP_DOMAIN),
                    'valid' => '/[0-9]/',
                    'default' => 10,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'generate_comic_page' => array(
                    'id'    => 'generate-page',
                    'type'  => 'checkbox',
                    'title'       => __('Generate Comic Page', MP_DOMAIN),
                    'description' => __('Generate a comic page based on values below.', MP_DOMAIN),
                    'valid'       => 'boolean',
                    'default'     => 1,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'comic_page_width'    => array(
                    'id'    => 'page-width',
                    'type'  => 'text',
                    'title'   => __('Comic Page Width', MP_DOMAIN),
                    'valid'   => '/[0-9]/',
                    'default' => 600,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'comic_page_height'   => array(
                    'id'    => 'page-height',
                    'type'  => 'text',
                    'title'   => __('Comic Page Height', MP_DOMAIN),
                    'valid'   => '/[0-9]/',
                    'default' => 1000,
                    'callback' => array($this, 'settings_field_cb'),
                ),
            ),
            'nav' => array(
                'insert_nav' => array(
                    'id'      => 'insert',
                    'title'   => __('Insert Navigation', MP_DOMAIN),
                    'description' => __('Automatically insert comic navigation code into comic posts.', MP_DOMAIN),
                    'type'    => 'checkbox',
                    'valid'   => 'boolean',
                    'default' => 1,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'nav_css'    => array(
                    'id'     => 'navigation-css',
                    'title'  => __('Navigation CSS', MP_DOMAIN),
                    'description' => __('Turn this off. You know you want to!', MP_DOMAIN),
                    'type'   => 'select',
                    'value'  => array(
                        'custom_css' => __('Custom CSS', MP_DOMAIN),
                        'default_css' => __('Default CSS', MP_DOMAIN),
                    ),
                    'valid'   => 'array',
                    'default' => 'custom_css',
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'display_css' => array(
                    'id'       => 'display',
                    'callback' => array($this, 'ft_navigation_css_display_cb'),
                )
            ),
        );

        return apply_filters('mangapress_options_fields', $options);
    }
    
   /**
     * Helper function for setting default options sections.
     *
     * @param array $sections Options sections/tabs
     * @return array
     */
    public function options_sections()
    {
        $sections = array(
            'basic'      => array(
                'title'       => __('Basic Options', MP_DOMAIN),
                'description' => __('This section sets the &ldquo;Latest-&rdquo; and &ldquo;Comic Archive&rdquo; pages, number of comics per page, and grouping comics together by category.', MP_DOMAIN),
            ),
            'comic_page' => array(
                'title'       => __('Comic Page Options', MP_DOMAIN),
                'description' => __('Handles image sizing options for comic pages. Thumbnail support may need to be enabled for some features to work properly. If page- or thumbnail sizes are changed, then a plugin such as Regenerate Thumbnails may be used to create the new thumbnails.', MP_DOMAIN),
            ),
            'nav'        => array(
                'title'       => __('Navigation Options', MP_DOMAIN),
                'description' => __('Options for comic navigation. Whether to have navigation automatically inserted on comic pages, or to enable/disable default comic navigation CSS.', MP_DOMAIN),
            ),
        );

        return apply_filters('mangapress_options_sections', $sections);
    }
    
    /**
     * Get option sections. Returned as an array based on the array keys from $sections
     * 
     * @return array
     */
    public function get_options_sections()
    {
        return array_keys($this->options_sections());
    }

}
