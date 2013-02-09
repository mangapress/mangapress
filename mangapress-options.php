<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */
require_once 'pages/options.php';

/**
 * MangaPress Options class
 * @package MangaPress
 * @subpackage MangaPress_Options
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */
class MangaPress_Settings extends MangaPress_Options
{
    /**
     * Options page View object
     *
     * @var \View_OptionsPage
     */
    protected $_view;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            array(
                'name'             => 'mangapress',
                'optiongroup_name' => 'mangapress_options',
                'options_field'    => $this->options_fields(),
                'sections'         => $this->options_sections(),
                'option_page'      => 'mangapress-options-page',
            )
        );

        // Syntax highlighter
        wp_register_script(
            'syntax-highlighter',
            MP_URLPATH . 'pages/js/syntaxhighlighter/scripts/shCore.js'
        );
        // the brush we need...
        wp_register_script(
            'syntax-highlighter-cssbrush',
            MP_URLPATH . 'pages/js/syntaxhighlighter/scripts/shBrushCss.js',
            array('syntax-highlighter')
        );
        // the style
        wp_register_style(
            'syntax-highlighter-css',
            MP_URLPATH . 'pages/js/syntaxhighlighter/styles/shCoreDefault.css'
        );

        add_action('admin_menu', array($this, 'admin_init'));
    }

    /**
     * Hook method for admin_menu.
     *
     * @return void
     */
    public function admin_init()
    {
        $this->set_view(
            new View_OptionsPage(
                array(
                    'path'       => MP_URLPATH, // plugin path
                    'post_type'  => null,
                    'js_scripts' => array(
                        'syntax-highlighter',
                        'syntax-highlighter-cssbrush'
                    ),
                    'css_styles' => array(
                        'syntax-highlighter-css',
                    ),
                    'ver'        => MP_VERSION,
                )
            )
        );

    }

    /**
     * Sets the current view object
     *
     * @param View_OptionsPage $view Sets the view object
     * @return \MangaPress_Options
     */
    public function set_view(View_OptionsPage $view)
    {
        $this->_view = $view;

        return $this;
    }

    /**
     * Retrieve the current view object
     *
     * @return \View_OptionsPage|\WP_Error
     */
    public function get_view()
    {
        if (!($this->_view instanceof MangaPress_View)) {
            return new WP_Error('not_view', '$this->_view is not an instance of View');
        }

        return $this->_view;
    }

    /**
     * Helper function for creating default options fields.
     *
     * @param array $options Option fields array.
     * @return array
     */
    public function options_fields($options = array())
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
                    'valid' => 'array',
                    'default' => 'post_date',
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'group_comics'      => array(
                    'id'    => 'group-comics',
                    'type'  => 'checkbox',
                    'title' => __('Group Comics', MP_DOMAIN),
                    'valid' => 'boolean',
                    'description' => __('Group comics by category. This option will ignore the parent category, and group according to the child-category.', MP_DOMAIN),
                    'default' => 1,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'group_by_parent'      => array(
                    'id'    => 'group-by-parent',
                    'type'  => 'checkbox',
                    'title' => __('Use Parent Category', MP_DOMAIN),
                    'valid' => 'boolean',
                    'description' => __('Group comics by top-most parent category. Use this option if you have sub-categories but want your navigation to function using the parent category.', MP_DOMAIN),
                    'default' => 1,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'latestcomic_page'  => array(
                    'id'    => 'latest-comic-page',
                    'type'  => 'select',
                    'title' => __('Latest Comic Page', MP_DOMAIN),
                    'value'    => array(
                        'no_val' => __('Select a Page', MP_DOMAIN),
                    ),
                    'valid' => 'array',
                    'default'  => 1,
                    'callback' => array($this, 'ft_basic_page_dropdowns_cb'),
                ),
                'latestcomic_page_template' => array(  // New option in 3.0
                    'id'    => 'latestcomic-page-template',
                    'type'  => 'checkbox',
                    'title'       => __('Use Template', MP_DOMAIN),
                    'description' => __('Use theme template for Latest Comic Page.', MP_DOMAIN),
                    'valid'       => 'boolean',
                    'default'     => 1,
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
                    'default' => 1,
                    'callback' => array($this, 'ft_basic_page_dropdowns_cb'),
                ),
                'comicarchive_page_template' => array(  // New option in 3.0
                    'id'    => 'comicarchive-page-template',
                    'type'  => 'checkbox',
                    'title'       => __('Use Template', MP_DOMAIN),
                    'description' => __('Use theme template for Comic Archive Page.', MP_DOMAIN),
                    'valid'       => 'boolean',
                    'default'     => 1,
                    'callback' => array($this, 'settings_field_cb'),
                ),
            ),
            'permalink' => array(

            ),
            'comic_page' => array(
                'banner_width'        => array(
                    'id'    => 'banner-width',
                    'type'  => 'text',
                    'title' => __('Banner Width', MP_DOMAIN),
                    'valid' => '/[0-9]/',
                    'default' => 450,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'banner_height'       =>  array(
                    'id'    => 'banner-height',
                    'type'  => 'text',
                    'title'   => __('Banner Height', MP_DOMAIN),
                    'valid'   => '/[0-9]/',
                    'default' => 100,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'comic_post_count'    =>  array( // New option in 3.0
                    'id'    => 'number-posts',
                    'type'  => 'text',
                    'title' => __('Comic Posts to Display', MP_DOMAIN),
                    'description' => __('Overrides values set in Reading Settings.', MP_DOMAIN),
                    'valid' => '/[0-9]/',
                    'default' => 10,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'generate_comic_page' => array(  // New option in 3.0
                    'id'    => 'generate-page',
                    'type'  => 'checkbox',
                    'title'       => __('Generate Comic Page', MP_DOMAIN),
                    'description' => __('Generate a comic page based on values below.', MP_DOMAIN),
                    'valid'       => 'boolean',
                    'default'     => 1,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'comic_page_width'    => array( // New option in 3.0
                    'id'    => 'page-width',
                    'type'  => 'text',
                    'title'   => __('Comic Page Width', MP_DOMAIN),
                    'valid'   => '/[0-9]/',
                    'default' => 600,
                    'callback' => array($this, 'settings_field_cb'),
                ),
                'comic_page_height'   => array( // New option in 3.0
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

        return $options;

    }

    /**
     * Helper function for setting default options sections.
     *
     * @param array $sections Options sections/tabs
     * @return array
     */
    public function options_sections($sections = array())
    {
        $sections = array(
            'basic'      => array(
                'title'       => __('Basic Options', MP_DOMAIN),
                'description' => __('This section sets the &ldquo;Latest-&rdquo; and &ldquo;Comic Archive&rdquo; pages, number of comics per page, and grouping comics together by category.', MP_DOMAIN),
            ),
            'permalink'  => array(
                'title'       => __('Comic Permalink Options', MP_DOMAIN),
                'description' => __('', MP_DOMAIN),
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

        return $sections;

    }

    /**
     * Outputs the settings fields
     *
     * @return void
     */
    public function output_settings_fields()
    {

        $field_sections = apply_filters('mangapress_option_fields', $this->options_fields());
        $current_tab    = $this->get_view()->get_current_tab();
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
     * @param type $option Current option array
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
     * @global type $mp
     * @param type $option Current option array
     * @return void
     */
    public function ft_navigation_css_display_cb($option)
    {
?>

<?php _e('Copy and paste this code into the <code>style.css</code> file of your theme.', MP_DOMAIN); ?>
<code style="display: block; width: 550px;"><pre class="brush: css;">

/* comic navigation */
.comic-navigation {
    text-align: center;
    margin: 5px 0 10px 0;
}

.comic-nav-span {
    padding: 3px 10px;
    text-decoration: none;
}

ul.comic-nav  {
    margin: 0;
    padding: 0;
    white-space: nowrap;
}

ul.comic-nav li {
    display: inline;
    list-style-type: none;
}

ul.comic-nav a {
    text-decoration: none;
    padding: 3px 10px;
}

ul.comic-nav a:link,
ul.comic-nav a:visited {
    color: #ccc;
    text-decoration: none;
}

ul.comic-nav a:hover { text-decoration: none; }
ul.comic-nav li:before{ content: ""; }

</pre></code>
    <?php
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
        $options = apply_filters('mangapress_option_section', $this->options_sections());

        $current = (substr($section['id'], strpos($section['id'], '-') + 1));

        echo "<p>{$options[$current]['description']}</p>";
    }

    /**
     * Sanitizes options before DB commit.
     *
     * @global type $mp
     * @param type $options Options array
     * @return type array
     */
    public function sanitize_options($options)
    {

        $mp_options = MangaPress_Bootstrap::get_instance()->get_options();

        $section           = key($options);
        $available_options = $this->options_fields();
        $new_options        = $mp_options;

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
                'group_comics'    => intval($options['basic']['group_comics']),
                'group_by_parent' => intval($options['basic']['group_by_parent']),
            );

            if ($options['basic']['latestcomic_page'] !== 'no_val'){
                $new_options['basic']['latestcomic_page'] = $options['basic']['latestcomic_page'];
            }

            $new_options['basic']['latestcomic_page_template'] = intval($options['basic']['latestcomic_page_template']);

            if ($options['basic']['comicarchive_page'] !== 'no_val'){
                $new_options['basic']['comicarchive_page'] = $options['basic']['comicarchive_page'];
            }

            $new_options['basic']['comicarchive_page_template'] = intval($options['basic']['comicarchive_page_template']);
        }

        if ($section == 'comic_page') {
            $new_options['comic_page'] = array(
                'make_thumb'          => intval($options['comic_page']['make_thumb']),
                'banner_width'        => intval($options['comic_page']['banner_width']),
                'banner_height'       => intval($options['comic_page']['banner_height']),
                'comic_post_count'    => intval($options['comic_page']['comic_post_count']),
                'generate_comic_page' => intval($options['comic_page']['generate_comic_page']),
                'comic_page_width'    => intval($options['comic_page']['comic_page_width']),
                'comic_page_height'   => intval($options['comic_page']['comic_page_height']),
            );
        }

        if ($section == 'permalink') {
            $new_options['permalink'] = array();
        }

        $options = array_merge($mp_options, $new_options);

        return $options;

    }

}
