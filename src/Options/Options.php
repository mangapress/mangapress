<?php


namespace MangaPress\Options;

/**
 * Class Options
 * @package MangaPress\Options
 */
class Options
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
     * Initialize options
     */
    public function init()
    {
        self::$options = maybe_unserialize(get_option());
    }

    /**
     * Get all currently set options
     * @return array
     */
    public static function get_options()
    {
        return self::$options;
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
}
