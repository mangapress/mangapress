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
    const OPTIONS_GROUP_NAME = 'mangapress_options';
    
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
    
    /**
     * PHP5 Constructor function
     * 
     * @return void
     */
    public function __construct()
    {
        add_action('admin_init', array($this, 'admin_init'));
    }
    
    /**
     * Run admin_init functions
     * 
     * @return void
     */
    public function admin_init()
    {
        
        if (defined('DOING_AJAX') && DOING_AJAX)
              return;
        
        register_setting(
            self::OPTIONS_GROUP_NAME,
            self::OPTIONS_GROUP_NAME,
            array($this, 'sanitize_options')
        );
        
        // register settings section
        $sections = $this->options_sections();
        foreach ($sections as $section_name => $data) {
            add_settings_section(
                self::OPTIONS_GROUP_NAME . "-{$section_name}",
                $data['title'],
                array($this, 'settings_section_cb'),
                self::OPTIONS_GROUP_NAME . "-{$section_name}"
            );
        }
        
        // output settings fields
        $this->output_settings_fields();
    }
    
    /**
     * Outputs the settings fields
     *
     * @return void
     */
    public function output_settings_fields()
    {
        $admin = MangaPress_Bootstrap::get_instance()->get_helper('admin');
        
        $field_sections = $this->options_fields();
        $current_tab    = $admin->get_current_tab();
        $fields         = $field_sections[$current_tab];

        foreach ($fields as $field_name => $field) {
            add_settings_field(
                "{$current_tab}-options-{$field['id']}",
                (isset($field['title']) ? $field['title'] : " "),
                $field['callback'],
                "mangapress_options-{$current_tab}",
                "mangapress_options-{$current_tab}",
                array_merge(array('name' => $field_name, 'section' => $current_tab), $field)
            );
        }
    }


    /**
     * Call-back for outputting settings fields
     *
     * @param array $option Current option array
     * @return void
     */
    public function settings_field_cb($option)
    {
        $mp_options = MangaPress_Bootstrap::get_instance()->get_options();
        
        $class = ucwords($option['type']);
        $value = $mp_options[$option['section']][$option['name']];

        if ($class !== ""){
            $attributes  = array(
                'name'  => "mangapress_options[{$option['section']}][{$option['name']}]",
                'id'    => $option['id'],
                'value' => $value,
            );

            $element = "MangaPress_{$class}";
            echo new $element(array(
                'attributes'  => $attributes,
                'description' => isset($option['description']) ? $option['description'] : '',
                'default'     => isset($option['value']) ? $option['value'] : $option['default'],
                'validation'  => $option['valid']
            ));
        }
    }
    
   /**
     * Call-back for outputting settings fields (select drop-downs)
     * with custom values.
     *
     * @global type $mp
     * @param type $option Current option array
     * @return void
     */
    public function ft_basic_page_dropdowns_cb($option)
    {
        
        $mp_options = MangaPress_Bootstrap::get_instance()->get_options();

        $value = $mp_options[$option['section']][$option['name']];

        $pages   = get_pages();
        $options = array_merge(array(), $option['value']);
        foreach($pages as $page) {
            $options[$page->post_name] = $page->post_title;
        }

        echo new MangaPress_Select(array(
            'attributes'  => array(
                'name'  => "mangapress_options[{$option['section']}][{$option['name']}]",
                'id'    => $option['id'],
                'value' => $value,
            ),
            'description' => isset($option['description']) ? $option['description'] : '',
            'default'     => $options,
            'validation'  => $option['valid']
        ));

    }

    /**
     * Call-back for outputting settings fields display box
     *
     * @param array $option Optional. Current option array
     * @return void
     */
    public function ft_navigation_css_display_cb($option = array())
    {
        require_once MP_ABSPATH . 'includes/pages/nav-css.php';
    }
    
    /**
     * settings_section_cb()
     * Outputs Settings Sections
     *
     * @param string $section Name of section
     * @return void
     */
    public function settings_section_cb($section)
    {
        $options = $this->options_sections();
        $current = (substr($section['id'], strpos($section['id'], '-') + 1));
        echo "<p>{$options[$current]['description']}</p>";
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
    
    /**
     * Sanitize options
     * 
     * @param array $options
     * @return array
     */
    public function sanitize_options($options)
    {
        if (!$options)
            return $options;
        
        $mp_options        = MangaPress_Bootstrap::get_instance()->get_options();        
        $section           = key($options);
        $available_options = $this->options_fields();
        $new_options       = $mp_options;

        if ($section == 'nav'){

            $new_options['nav']['insert_nav'] = intval($options['nav']['insert_nav']);

            //
            // if the value of the option doesn't match the correct values in the array, then
            // the value of the option is set to its default.
            $nav_css_values = array_keys($available_options['nav']['nav_css']['value']);

            if (in_array($mp_options['nav']['nav_css'], $nav_css_values)){
                $new_options['nav']['nav_css'] = strval($options['nav']['nav_css']);
            } else {
                $new_options['nav']['nav_css'] = 'default_css';
            }
        }

        if ($section == 'basic') {
            $order_by_values = array_keys($available_options['basic']['order_by']['value']);
            //
            // Converting the values to their correct data-types should be enough for now...
            $new_options['basic'] = array(
                'order_by'        => (in_array($options['basic']['order_by'], $order_by_values))
                                            ? strval($options['basic']['order_by']) : 'post_date',
                'group_comics'    => $this->_sanitize_integer($options, 'basic', 'group_comics'),
                'group_by_parent' => $this->_sanitize_integer($options, 'basic', 'group_by_parent'),
            );

            if ($options['basic']['latestcomic_page'] !== 'no_val'){
                $new_options['basic']['latestcomic_page'] = $options['basic']['latestcomic_page'];
            } else {
                $new_options['basic']['latestcomic_page'] = 0;
            }

            $new_options['basic']['latestcomic_page_template'] 
                    = $this->_sanitize_integer($options, 'basic', 'latestcomic_page_template');

            if ($options['basic']['comicarchive_page'] !== 'no_val') {
                $new_options['basic']['comicarchive_page'] = $options['basic']['comicarchive_page'];
            } else {
                $new_options['basic']['comicarchive_page'] = 0;
            }

            $new_options['basic']['comicarchive_page_template'] 
                    = intval($options['basic']['comicarchive_page_template']);
        }

        if ($section == 'comic_page') {
            $new_options['comic_page'] = array(
                'comic_post_count'    => $this->_sanitize_integer($options, 'comic_page', 'comic_post_count'),
                'generate_comic_page' => $this->_sanitize_integer($options, 'comic_page','generate_comic_page'),
                'comic_page_width'    => $this->_sanitize_integer($options, 'comic_page','comic_page_width'),
                'comic_page_height'   => $this->_sanitize_integer($options, 'comic_page','comic_page_height'),
            );
        }

        return array_merge($mp_options, $new_options);
    }
    
    /**
     * Sanitize integers
     * 
     * @param array $option_array
     * @param string $section
     * @param string $name
     * 
     * @return mixed
     */
    private function _sanitize_integer($option_array, $section, $name)
    {
        return isset($option_array[$section][$name]) 
                ? intval($option_array[$section][$name]) : 0;                
    }
}
