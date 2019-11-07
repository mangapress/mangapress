<?php
/**
 * Functions for Admin class
 */

namespace MangaPress\Admin\Functions;

/**
 * Get the current tab
 * @return string
 */
function get_current_tab()
{
    $tabs        = array_keys(options_tabs());
    $current_tab = filter_input(INPUT_GET, 'tab')
        ? filter_input(INPUT_GET, 'tab') : 'basic';

    if (in_array($current_tab, $tabs)) {
        return $current_tab;
    } else {
        return 'basic';
    }
}

/**
 * Return static array of options sections configuration
 * @return array
 */
function options_tabs()
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

    /**
     * mangapress_options_sections
     *
     * Allow 3rd party themes and plugins to add their own sections to Manga+Press options
     * @param array $sections
     * @return array
     */
    return apply_filters('mangapress_options_sections', $sections);
}
