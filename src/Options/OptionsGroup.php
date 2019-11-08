<?php
/**
 * OptionsGroup
 *
 * @package MangaPress\Options
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\Options;

use MangaPress\PluginComponent;
use function MangaPress\Admin\Functions\get_current_tab;
use function MangaPress\Admin\Functions\options_tabs;

/**
 * Class OptionsGroup
 * @package MangaPress\Options
 */
class OptionsGroup implements PluginComponent
{
    const OPTIONS_GROUP_NAME = 'mangapress_options';

    /**
     * Story object for later use
     * @var OptionsGroup
     */
    protected static $instance;

    /**
     * OptionsGroup constructor.
     * Initialize object
     */
    public function __construct()
    {
        self::$instance = $this;
    }

    /**
     * Get the object instance that was originally created
     * @return OptionsGroup
     */
    public static function get_instance()
    {
        return self::$instance;
    }

    /**
     * Initialize options group
     */
    public function init()
    {
        add_action('admin_init', [$this, 'options_group_init']);
    }

    /**
     * Initialize OptionsGroup
     */
    public function options_group_init()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        register_setting(
            self::OPTIONS_GROUP_NAME,
            self::OPTIONS_GROUP_NAME,
            [
                'sanitize_callback' => [$this, 'sanitize_options'],
            ]
        );

        $sections = $this->options_sections();
        foreach ($sections as $section_name => $data) {
            \add_settings_section(
                self::OPTIONS_GROUP_NAME . "-{$section_name}",
                $data['title'],
                [$this, 'settings_section_cb'],
                self::OPTIONS_GROUP_NAME . "-{$section_name}"
            );
        }

        $this->output_settings_fields();
    }

    /**
     * Build options sections
     * @return array
     */
    public function options_sections()
    {
        return options_tabs();
    }

    /**
     * Output settings fields and groups
     */
    public function output_settings_fields()
    {
        $field_sections = $this->options_fields();
        $current_tab    = get_current_tab();
        $fields         = $field_sections[$current_tab];

        foreach ($fields as $field_name => $field) {
            add_settings_field(
                "{$current_tab}-options-{$field['id']}",
                (isset($field['title']) ? $field['title'] : " "),
                $field['callback'],
                "mangapress_options-{$current_tab}",
                "mangapress_options-{$current_tab}",
                array_merge(['name' => $field_name, 'section' => $current_tab], $field)
            );
        }
    }

    /**
     * Options field config
     * @return array
     */
    public function options_fields()
    {
        /*
         * Section
         *      |_ Option
         *              |_ Option Setting
         */
        $options = [
            'basic'      => [
                'latestcomic_page'        => [
                    'id'       => 'latest-comic-page',
                    'type'     => 'page-select',
                    'title'    => __('Latest Comic Page', MP_DOMAIN),
                    'value'    => [
                        'no_val' => __('Select a Page', MP_DOMAIN),
                    ],
                    'valid'    => 'array',
                    'default'  => '',
                    'callback' => [$this, 'settings_field_cb'],
                ],
                'comicarchive_page'       => [
                    'id'       => 'archive-page',
                    'type'     => 'page-select',
                    'title'    => __('Comic Archive Page', MP_DOMAIN),
                    'value'    => [
                        'no_val' => __('Select a Page', MP_DOMAIN),
                    ],
                    'valid'    => 'array',
                    'default'  => '',
                    'callback' => [$this, 'settings_field_cb'],
                ],
                'group_comics'            => [
                    'id'          => 'group-comics',
                    'type'        => 'checkbox',
                    'title'       => __('Group Comics', MP_DOMAIN),
                    'valid'       => 'boolean',
                    'description' => __(
                        'Group comics by category. This option will ignore the parent category, '
                        . 'and group according to the child-category.',
                        MP_DOMAIN
                    ),
                    'default'     => 1,
                    'callback'    => [$this, 'settings_field_cb'],
                ],
                'group_by_parent'         => [
                    'id'          => 'group-by-parent',
                    'type'        => 'checkbox',
                    'title'       => __('Use Parent Category', MP_DOMAIN),
                    'valid'       => 'boolean',
                    'description' => __(
                        'Group comics by top-most parent category. Use this option if you have sub-categories but '
                        . 'want your navigation to function using the parent category.',
                        MP_DOMAIN
                    ),
                    'default'     => 1,
                    'callback'    => [$this, 'settings_field_cb'],
                ],
                'comicarchive_page_style' => [
                    'id'          => 'archive-page-style',
                    'type'        => 'select',
                    'title'       => __('Comic Archive Page Style', MP_DOMAIN),
                    'description' => __(
                        'Style used for comic archive page. List, Calendar, or Gallery. Default: List',
                        MP_DOMAIN
                    ),
                    'value'       => [
                        'no_val'   => __('Select a Style', MP_DOMAIN),
                        'list'     => __('List', MP_DOMAIN),
                        'calendar' => __('Calendar', MP_DOMAIN),
                        'gallery'  => __('Gallery', MP_DOMAIN),
                    ],
                    'valid'       => 'array',
                    'default'     => 'list',
                    'callback'    => [$this, 'settings_field_cb'],
                ],
                'archive_order'           => [
                    'id'          => 'order',
                    'title'       => __('Archive Page Comic Order', MP_DOMAIN),
                    'description' => __(
                        'Designates the ascending or descending order of the orderby parameter',
                        MP_DOMAIN
                    ),
                    'type'        => 'select',
                    'value'       => [
                        'ASC'  => __('ASC', MP_DOMAIN),
                        'DESC' => __('DESC', MP_DOMAIN),
                    ],
                    'valid'       => 'array',
                    'default'     => 'DESC',
                    'callback'    => [$this, 'settings_field_cb'],
                ],
                'archive_orderby'         => [
                    'id'          => 'orderby',
                    'title'       => __('Archive Page Comic Order By', MP_DOMAIN),
                    'description' => __('Sort retrieved posts according to selected parameter.', MP_DOMAIN),
                    'type'        => 'select',
                    'value'       => [
                        'ID'       => __('Order by Post ID', MP_DOMAIN),
                        'author'   => __('Order by author', MP_DOMAIN),
                        'title'    => __('Order by title', MP_DOMAIN),
                        'name'     => __('Order by post name (post slug)', MP_DOMAIN),
                        'date'     => __('Order by date.', MP_DOMAIN),
                        'modified' => __('Order by last modified date.', MP_DOMAIN),
                        'rand'     => __('Random order', MP_DOMAIN),
                    ],
                    'valid'       => 'array',
                    'default'     => 'date',
                    'callback'    => [$this, 'settings_field_cb'],
                ],
            ],
            'comic_page' => [
                'generate_comic_page' => [
                    'id'          => 'generate-page',
                    'type'        => 'checkbox',
                    'title'       => __('Generate Comic Page', MP_DOMAIN),
                    'description' => __('Generate a comic page based on values below.', MP_DOMAIN),
                    'valid'       => 'boolean',
                    'default'     => 1,
                    'callback'    => [$this, 'settings_field_cb'],
                ],
                'comic_page_width'    => [
                    'id'       => 'page-width',
                    'type'     => 'text',
                    'title'    => __('Comic Page Width', MP_DOMAIN),
                    'valid'    => '/[0-9]/',
                    'default'  => 600,
                    'callback' => [$this, 'settings_field_cb'],
                ],
                'comic_page_height'   => [
                    'id'       => 'page-height',
                    'type'     => 'text',
                    'title'    => __('Comic Page Height', MP_DOMAIN),
                    'valid'    => '/[0-9]/',
                    'default'  => 1000,
                    'callback' => [$this, 'settings_field_cb'],
                ],
            ],
            'nav'        => [
                'nav_css'     => [
                    'id'          => 'navigation-css',
                    'title'       => __('Navigation CSS', MP_DOMAIN),
                    'description' => __(
                        'Include the default CSS for the navigation. Set to Custom CSS '
                        . '(which uses styles defined by the theme).',
                        MP_DOMAIN
                    ),
                    'type'        => 'select',
                    'value'       => [
                        'custom_css'  => __('Custom CSS', MP_DOMAIN),
                        'default_css' => __('Default CSS', MP_DOMAIN),
                    ],
                    'valid'       => 'array',
                    'default'     => 'custom_css',
                    'callback'    => [$this, 'settings_field_cb'],
                ],
                'display_css' => [
                    'id'       => 'display',
                    'callback' => '\MangaPress\Options\Fields\Functions\navigation_css_display_cb',
                ],
            ],
        ];

        /**
         * mangapress_options_fields
         * Filter options settings. Allows plugin developers to add their own options.
         * @param array $options Default options array
         * @since 2.9
         */
        return apply_filters('mangapress_options_fields', $options);
    }

    /**
     * Callback function to generate field markup
     * @param array $option
     */
    public function settings_field_cb($option)
    {
        $class = str_replace(' ', '', ucwords(str_replace('-', ' ', $option['type'])));
        $value = Options::get_option($option['name'], $option['section']);

        if ($class !== "") {
            $attributes = [
                'name'  => "mangapress_options[{$option['section']}][{$option['name']}]",
                'id'    => $option['id'],
                'value' => $value,
            ];

            $element = 'MangaPress\Options\Fields\Types\\' . $class;

            echo new $element(
                [
                    'attributes'  => $attributes,
                    'description' => isset($option['description']) ? $option['description'] : '',
                    'default'     => isset($option['value']) ? $option['value'] : $option['default'],
                    'validation'  => $option['valid'],
                ]
            );
        }
    }

    /**
     * settings_section_cb()
     * Outputs Settings Sections
     *
     * @param string $section Name of section
     *
     * @return void
     */
    public function settings_section_cb($section)
    {
        $options = $this->options_sections();
        $current = (substr($section['id'], strpos($section['id'], '-') + 1));
        echo "<p>{$options[$current]['description']}</p>";
    }

    /**
     * Sanitize options
     * @param array $options
     * @return array
     */
    public function sanitize_options($options)
    {
        if (!$options) {
            return $options;
        }

        $section           = key($options);
        $available_options = self::options_fields();
        $default           = Options::get_options();
        $new_options       = $default;

        if ($section === 'nav') {
            //
            // if the value of the option doesn't match the correct values in the array, then
            // the value of the option is set to its default.
            $nav_css_values = array_keys($available_options['nav']['nav_css']['value']);

            if (in_array($default['nav']['nav_css'], $nav_css_values)) {
                $new_options['nav']['nav_css'] = strval($options['nav']['nav_css']);
            } else {
                $new_options['nav']['nav_css'] = 'default_css';
            }
        }

        if ($section == 'basic') {
            $archive_order_values   = array_keys($available_options['basic']['archive_order']['value']);
            $archive_orderby_values = array_keys($available_options['basic']['archive_orderby']['value']);
            //
            // Converting the values to their correct data-types should be enough for now...
            $new_options['basic'] = [
                'archive_order'   => in_array($options['basic']['archive_order'], $archive_order_values)
                    ? $options['basic']['archive_order']
                    : $available_options['basic']['archive_order']['default'],
                'archive_orderby' => in_array($options['basic']['archive_orderby'], $archive_orderby_values)
                    ? $options['basic']['archive_orderby']
                    : $available_options['basic']['archive_orderby']['default'],
                'group_comics'    => $this->sanitize_integer($options, 'basic', 'group_comics'),
                'group_by_parent' => $this->sanitize_integer($options, 'basic', 'group_by_parent'),
            ];

            if ($options['basic']['latestcomic_page'] !== 'no_val') {
                $new_options['basic']['latestcomic_page'] = $options['basic']['latestcomic_page'];
            } else {
                $new_options['basic']['latestcomic_page'] = '';
            }

            if ($options['basic']['comicarchive_page'] !== 'no_val') {
                $new_options['basic']['comicarchive_page'] = $options['basic']['comicarchive_page'];
            } else {
                $new_options['basic']['comicarchive_page'] = '';
            }

            if ($options['basic']['comicarchive_page_style'] !== 'no_val') {
                $new_options['basic']['comicarchive_page_style'] = $options['basic']['comicarchive_page_style'];
            } else {
                $new_options['basic']['comicarchive_page_style'] = 'list';
            }

            // add a later check for rewrite rules to be updated on init
            add_option('mangapress_flush_rewrite_rules', true, '', 'no');
        }

        if ($section == 'comic_page') {
            $new_options['comic_page'] = [
                'generate_comic_page' => $this->sanitize_integer($options, 'comic_page', 'generate_comic_page'),
                'comic_page_width'    => $this->sanitize_integer($options, 'comic_page', 'comic_page_width'),
                'comic_page_height'   => $this->sanitize_integer($options, 'comic_page', 'comic_page_height'),
            ];
        }

        return array_merge($default, $new_options);
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
    private function sanitize_integer($option_array, $section, $name)
    {
        return isset($option_array[$section][$name])
            ? intval($option_array[$section][$name]) : 0;
    }
}
