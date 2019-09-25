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
    protected static $options;

    /**
     * Default options—used on initial plugin activation
     * @var array
     */
    protected static $default_options;

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
