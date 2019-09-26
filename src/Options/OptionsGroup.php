<?php


namespace MangaPress\Options;

use MangaPress\Bootstrap;
use MangaPress\Options\Fields;

/**
 * Class OptionsGroup
 * @package MangaPress\Options
 */
class OptionsGroup
{
    const OPTIONS_GROUP_NAME = 'mangapress_options';

    /**
     * Initialize OptionsGroup
     */
    public function init()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        register_setting(
            self::OPTIONS_GROUP_NAME,
            self::OPTIONS_GROUP_NAME,
            [$this, 'sanitize_options']
        );

        $sections = $this->options_sections();
        foreach ($sections as $section_name => $data) {
            add_settings_section(
                self::OPTIONS_GROUP_NAME . "-{$section_name}",
                $data['title'],
                [$this, 'settings_section_cb'],
                self::OPTIONS_GROUP_NAME . "-{$section_name}"
            );
        }

        $this->output_settings_fields();
    }

    /**
     * Output settings fields and groups
     */
    public function output_settings_fields()
    {
        $field_sections = $this->options_fields();
        $current_tab    = 'basic' ; //Admin::get_current_tab();
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

    public function settings_section_cb($option)
    {
        $mp_options = Options::get_options();

        $class = str_replace('-', '', ucwords($option['type']));
        $value = isset($mp_options[$option['section']][$option['name']])
            ? $mp_options[$option['section']][$option['name']]
            : self::$default_options[$option['section']][$option['name']];

        if ($class !== "") {
            $attributes = [
                'name'  => "mangapress_options[{$option['section']}][{$option['name']}]",
                'id'    => $option['id'],
                'value' => $value,
            ];

            $element = $class;

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
     * Options field config
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
                    'callback' => [$this, 'ft_basic_page_dropdowns_cb'],
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
                    'callback' => [$this, 'ft_basic_page_dropdowns_cb'],
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
                    'callback' => [$this, 'ft_navigation_css_display_cb'],
                ],
            ],
        ];

        return apply_filters('mangapress_options_fields', $options);
    }

    /**
     * Build options sections
     * @return array
     */
    public function options_sections()
    {
        $sections = [
            'basic'      => [
                'title'       => __('Basic Options', MP_DOMAIN),
                'description' => __(
                    'This section sets the &ldquo;Latest-&rdquo; and &ldquo;Comic Archive&rdquo; pages, '
                    . 'number of comics per page, and grouping comics together by category.',
                    MP_DOMAIN
                ),
            ],
            'comic_page' => [
                'title'       => __('Comic Page Options', MP_DOMAIN),
                'description' => __(
                    'Handles image sizing options for comic pages. Thumbnail support may need to be '
                    . 'enabled for some features to work properly. If page- or thumbnail sizes are changed, '
                    . 'then a plugin such as Regenerate Thumbnails may be used to create the new thumbnails.',
                    MP_DOMAIN
                ),
            ],
            'nav'        => [
                'title'       => __('Navigation Options', MP_DOMAIN),
                'description' => __(
                    'Options for comic navigation. Whether to have navigation automatically inserted on comic pages, '
                    . 'or to enable/disable default comic navigation CSS.',
                    MP_DOMAIN
                ),
            ],
        ];

        return apply_filters('mangapress_options_sections', $sections);
    }

    public function sanitize_options()
    {
        //
    }
}
