<?php
/**
 * MangaPress
 *
 * @package mangapress-options
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */
namespace MangaPress\Plugin;

/**
 * mangapress-options
 *
 * @author Jess Green <jgreen at psy-dreamer.com>
 */
class Options
{
    const OPTIONS_GROUP_NAME = 'mangapress_options';

    /**
     * Default options array
     *
     * @var array
     */
    protected static $default_options = null;

    protected static $instance = null;

    /**
     * Init class
     */
    public static function init()
    {
        add_action('admin_init', array(__CLASS__, 'admin_init'));
    }

    /**
     * Run admin_init functions
     */
    public static function admin_init()
    {
        if (defined('DOING_AJAX') && DOING_AJAX)
            return;

        register_setting(
            self::OPTIONS_GROUP_NAME,
            self::OPTIONS_GROUP_NAME,
            array(__CLASS__, 'sanitize_options')
        );

        // register settings section
        $sections = self::options_fields();

        foreach ($sections as $section_name => $data) {
            add_settings_section(
                self::OPTIONS_GROUP_NAME . "-{$section_name}",
                $data['section']['title'],
                array(__CLASS__, 'settings_section_cb'),
                self::OPTIONS_GROUP_NAME . "-{$section_name}"
            );
        }

        // output settings fields
        self::output_settings_fields();
    }

    /**
     * Outputs the settings fields
     *
     * @return void
     */
    public static function output_settings_fields()
    {
        $admin = Bootstrap::get_instance()->get_helper('admin');

        $field_sections = self::options_fields();
        $current_tab = $admin->get_current_tab();
        $fields = $field_sections[$current_tab]['fields'];

        foreach ($fields as $field_name => $field) {
            $field = wp_parse_args($field, [
                'callback' => array(__CLASS__, 'settings_field_cb'),
                'value' => '',
            ]);

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
     *
     * @return void
     */
    public static function settings_field_cb($option)
    {
        $mp_options = Bootstrap::get_instance()->get_options();
        $default_options = self::get_default_options();

        $class = ucwords($option['type']);
        $value = isset($mp_options[$option['section']][$option['name']])
            ? $mp_options[$option['section']][$option['name']]
            : $default_options[$option['section']][$option['name']];

        if ($class !== "") {
            $attributes = array(
                'name' => "mangapress_options[{$option['section']}][{$option['name']}]",
                'id' => $option['id'],
                'value' => $value,
            );

            $element = "MangaPress\Form\Element\\{$class}";
            $params = array(
                'attributes' => $attributes,
                'description' => isset($option['description']) ? $option['description'] : '',
                'value' => ($value !== $option['value']) ? $value : $option['value'],
                'default' => $option['default'],
            );
            if ($class == 'Checkbox') {
                $params['hidden'] = $option['default'];
                $params['checked'] = $value;
                $params['value'] = $option['value'];
            }

            echo new $element($params);
        }
    }

    /**
     * Get array of pages from database
     * @return array
     */
    private static function get_page_values()
    {
        $pages = get_pages();
        $options['no_val'] = __('Select a Page', MP_DOMAIN);
        foreach ($pages as $page) {
            $options[$page->post_name] = $page->post_title;
        }
        return $options;
    }

    /**
     * Call-back for outputting settings fields display box
     *
     * @param array $option Optional. Current option array
     *
     * @return void
     */
    public static function ft_navigation_css_display_cb($option = array())
    {
        require_once MP_ABSPATH . 'includes/pages/nav-css.php';
    }

    /**
     * settings_section_cb()
     * Outputs Settings Sections
     *
     * @param string $section Name of section
     *
     * @return void
     */
    public static function settings_section_cb($section)
    {
        $options = self::options_sections();
        $current = (substr($section['id'], strpos($section['id'], '-') + 1));
        echo "<p>{$options[$current]['description']}</p>";
    }


    /**
     * Returns default options
     * Used by Install to handle defaults on activation
     *
     * @return array
     */
    public static function get_default_options()
    {
        if (self::$default_options === null) {
            $options = self::options_fields();
            $default_option_values = [];
            foreach ($options as $section => $option) {
                if (!isset($default_option_values[$section])) {
                    $default_option_values[$section] = [];
                }

                foreach ($option['fields'] as $name => $field) {
                    if (!isset($field['default'])) continue;

                    $default_option_values[$section][$name] = $field['default'];
                }
            }

            self::$default_options = $default_option_values;
        }

        return self::$default_options;
    }

    /**
     * Helper function for creating default options fields.
     *
     * @return array
     */
    public static function options_fields()
    {
        /*
         * Section
         *      |_ Option
         *              |_ Option Setting
         */
        $options = array(
            'basic' => array(
                'section' => array(
                    'title' => __('Basic Options', MP_DOMAIN),
                    'description' => __('This section sets the &ldquo;Latest-&rdquo; and &ldquo;Comic Archive&rdquo; pages, number of comics per page, and grouping comics together by category.', MP_DOMAIN),
                ),
                'fields' => array(
                    'latestcomic_page' => array(
                        'id' => 'latest-comic-page',
                        'type' => 'select',
                        'title' => __('Latest Comic Page', MP_DOMAIN),
                        'default' => self::get_page_values(),
                        'value' => 'no_val',
                    ),
                    'group_comics' => array(
                        'id' => 'group-comics',
                        'type' => 'checkbox',
                        'title' => __('Group Comics', MP_DOMAIN),
                        'description' => __('Group comics by category. This option will ignore the parent category, and group according to the child-category.', MP_DOMAIN),
                        'default' => false,
                        'value' => true,
                        'callback' => array(__CLASS__, 'settings_field_cb'),
                    ),
                    'group_by_parent' => array(
                        'id' => 'group-by-parent',
                        'type' => 'checkbox',
                        'title' => __('Use Parent Category', MP_DOMAIN),
                        'description' => __('Group comics by top-most parent category. Use this option if you have sub-categories but want your navigation to function using the parent category.', MP_DOMAIN),
                        'default' => false,
                        'value' => true,
                        'callback' => array(__CLASS__, 'settings_field_cb'),
                    ),
                    'comicarchive_page' => array(
                        'id' => 'archive-page',
                        'type' => 'select',
                        'title' => __('Comic Archive Page', MP_DOMAIN),
                        'default' => self::get_page_values(),
                        'value' => 'no_val',
                    ),
                    'comicarchive_page_style' => array(
                        'id' => 'archive-page-style',
                        'type' => 'select',
                        'title' => __('Comic Archive Page Style', MP_DOMAIN),
                        'description' => __('Style used for comic archive page. List, Calendar, or Gallery. Default: List', MP_DOMAIN),
                        'value' => 'list',
                        'default' => array(
                            'no_val' => __('Select a Style', MP_DOMAIN),
                            'list' => __('List', MP_DOMAIN),
                            'calendar' => __('Calendar', MP_DOMAIN),
                            'gallery' => __('Gallery', MP_DOMAIN),
                        ),
                    ),
                    'archive_order' => array(
                        'id' => 'order',
                        'title' => __('Archive Page Comic Order', MP_DOMAIN),
                        'description' => __('Designates the ascending or descending order of the orderby parameter', MP_DOMAIN),
                        'type' => 'select',
                        'value' => 'DESC',
                        'default' => array(
                            'ASC' => __('ASC', MP_DOMAIN),
                            'DESC' => __('DESC', MP_DOMAIN),
                        ),
                    ),
                    'archive_orderby' => array(
                        'id' => 'orderby',
                        'title' => __('Archive Page Comic Order By', MP_DOMAIN),
                        'description' => __('Sort retrieved posts according to selected parameter.', MP_DOMAIN),
                        'type' => 'select',
                        'value' => 'date',
                        'default' => array(
                            'ID' => __('Order by Post ID', MP_DOMAIN),
                            'author' => __('Order by author', MP_DOMAIN),
                            'title' => __('Order by title', MP_DOMAIN),
                            'name' => __('Order by post name (post slug)', MP_DOMAIN),
                            'date' => __('Order by date.', MP_DOMAIN),
                            'modified' => __('Order by last modified date.', MP_DOMAIN),
                            'rand' => __('Random order', MP_DOMAIN),
                        ),
                    ),
                )
            ),
            'comic_page' => array(
                'section' => array(
                    'title' => __('Comic Page Options', MP_DOMAIN),
                    'description' => __('Controls appearance of comic page. If page- or thumbnail sizes are changed, then a plugin such as Regenerate Thumbnails may be used to create the new thumbnails.', MP_DOMAIN),
                ),
                'fields' => array(
                    'enable_comic_lightbox' => array(
                        'id' => 'enable-comic-lightbox',
                        'type' => 'checkbox',
                        'title' => __('Enable Lightbox', MP_DOMAIN),
                        'description' => __('Allow comic to be displayed in a full-screen lightbox.', MP_DOMAIN),
                        'default' => false,
                        'value' => true,
                    ),
                    'generate_comic_page' => array(
                        'id' => 'generate-page',
                        'type' => 'checkbox',
                        'title' => __('Generate Comic Page', MP_DOMAIN),
                        'description' => __('Generate a comic page based on values below.', MP_DOMAIN),
                        'default' => false,
                        'value' => true,
                    ),
                    'comic_page_width' => array(
                        'id' => 'page-width',
                        'type' => 'number',
                        'title' => __('Comic Page Width', MP_DOMAIN),
                        'default' => 600,
                    ),
                    'comic_page_height' => array(
                        'id' => 'page-height',
                        'type' => 'number',
                        'title' => __('Comic Page Height', MP_DOMAIN),
                        'default' => 1000,
                    ),
                )
            ),
            'nav' => array(
                'section' => array(
                    'title' => __('Navigation Options', MP_DOMAIN),
                    'description' => __('Options for comic navigation. Whether to have navigation automatically inserted on comic pages, or to enable/disable default comic navigation CSS.', MP_DOMAIN),
                ),
                'fields' => array(
                    'nav_css' => array(
                        'id' => 'navigation-css',
                        'title' => __('Navigation CSS', MP_DOMAIN),
                        'description' => __('Include the default CSS for the navigation. Set to Custom CSS (which uses styles defined by the theme).', MP_DOMAIN),
                        'type' => 'select',
                        'default' => array(
                            'custom_css' => __('Custom CSS', MP_DOMAIN),
                            'default_css' => __('Default CSS', MP_DOMAIN),
                        ),
                        'value' => 'custom_css',
                    ),
                    'display_css' => array(
                        'id' => 'display',
                        'callback' => array(__CLASS__, 'ft_navigation_css_display_cb'),
                    )
                )
            ),
        );
        return apply_filters('mangapress_options_fields', $options);
    }

    /**
     * Helper function for setting default options sections.
     *
     * @return array
     */
    public static function options_sections()
    {
        $fields = self::options_fields();
        $sections = array();
        foreach ($fields as $section => $data) {
            $sections[$section] = $data['section'];
        }
        return apply_filters('mangapress_options_sections', $sections);
    }


    /**
     * Get option sections. Returned as an array based on the array keys from $sections
     *
     * @return array
     */
    public static function get_options_sections()
    {
        return array_keys(self::options_sections());
    }


    /**
     * Sanitize options
     *
     * @param array $options
     *
     * @return array
     */
    public static function sanitize_options($options)
    {
        if (!$options)
            return $options;

        $mp_options = Bootstrap::get_instance()->get_options();
        $section = key($options);
        $available_options = self::options_fields();
        $new_options = $mp_options;

        foreach ($options[$section] as $option_name => $option) {
            if (isset($available_options[$section]['fields'][$option_name]['sanitize_callback'])) {
                $sanitize_cb = $available_options[$section]['fields'][$option_name]['sanitize_callback'];

                if (is_string($sanitize_cb)) {
                    $new_options[$section][$option_name] = $sanitize_cb($option);
                } else if (is_array($sanitize_cb)) {
                    $cb = $sanitize_cb[0];
                    $new_options[$section][$option_name] = $cb($option, $sanitize_cb[1]);
                }
            } else {
                // process according to field-type
                $new_options[$section][$option_name] =
                    self::sanitize_field(
                        $option,
                        $available_options[$section]['fields'][$option_name]
                    );
            }
        }

        return array_merge($mp_options, $new_options);
    }


    /**
     * Sanitize fields according to type
     *
     * @param mixed $option
     * @param array $config
     *
     * @return bool|int
     */
    private static function sanitize_field($option, $config)
    {
        $type = $config['type'];
        switch ($type) {
            case 'checkbox':
                return boolval($option);
                break;
            case 'number':
                return intval($option);
                break;
            case 'select':
                if (in_array($option, array_keys($config['default']))) {
                    return $option;
                } else {
                    return $config['value'];
                }
                break;
            case 'text':
            case 'textarea':
                return filter_var($option, FILTER_SANITIZE_STRING);
                break;
            default:
                return apply_filters("mangapress_sanitize_{$type}", $option);
        }
    }
}
