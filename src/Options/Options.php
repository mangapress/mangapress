<?php
/**
 * Options handlers
 *
 * @package MangaPress\Options
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\Options;

use MangaPress\PluginComponent;

/**
 * Class Options
 * @package MangaPress\Options
 */
class Options implements PluginComponent
{
    /**
     * Options array
     * @var array $options
     */
    protected static $options = [];

    /**
     * Default options—used on initial plugin activation
     * @var array $default_options
     */
    protected static $default_options = [
        'basic'      => [
            'latestcomic_page'        => '',
            'comicarchive_page'       => '',
            'group_comics'            => 0,
            'group_by_parent'         => 0,
            'comicarchive_page_style' => 'list',
            'archive_order'           => 'DESC',
            'archive_orderby'         => 'date',
        ],
        'comic_page' => [
            'generate_comic_page' => 0,
            'comic_page_width'    => 600,
            'comic_page_height'   => 1000,
        ],
        'nav'        => [
            'nav_css' => 'custom_css',
        ],
    ];

    /**
     * Get all currently set options
     * @return array
     */
    public static function get_options()
    {
        return array_merge(self::$options, self::$default_options);
    }

    /**
     * Get option value
     *
     * @param string $name
     * @param string $section
     * @return mixed
     */
    public static function get_option($name, $section)
    {
        if (isset(self::$options[$section][$name])) {
            return self::$options[$section][$name];
        }

        return self::$default_options[$section][$name];
    }

    /**
     * Initialize options
     */
    public function init()
    {
        self::$options = maybe_unserialize(get_option(OptionsGroup::OPTIONS_GROUP_NAME));
    }
}
