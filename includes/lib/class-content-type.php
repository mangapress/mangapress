<?php
/**
 * WordPress_PostType_Framework
 *
 * So wish WordPress would drop PHP 5.2 support.
 * Namespaces would be very handy
 *
 * @package    WordPress_PostType_Framework
 * @subpackage MangaPress_FrameWork_Helper
 * @author     Jess Green <support@manga-press.com>
 * @version    $Id$
 */
namespace MangaPress\Lib;
/**
 * ContentType
 * This abstract class contains basic properties and methods
 * used by the PostType and Taxonomy classes.
 *
 * @package ContentType
 * @author  Jess Green <support@manga-press.com>
 */
abstract class ContentType
{

    /**
     * Object name
     *
     * @var string
     */
    protected $name;

    /**
     * Object singular (human-readable) label
     *
     * @var string
     */
    protected $label_single;

    /**
     * Object plural (human-readable) label
     *
     * @var string
     */
    protected $label_plural;

    /**
     * Text domain string for i8n
     * Must be set before arguments!
     *
     * @var string
     */
    protected $textdomain = '';

    /**
     * Object arguments
     *
     * @var array
     */
    protected $args;

    /**
     * Object init
     *
     * @return void
     */
    abstract public function init();

    /**
     * PHP5 Constructor method
     *
     * @param array $options Optional. Pass Object parameters on construct
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->set_options($options)
                ->init();
        }
    }

    /**
     * Set the object name
     *
     * @param  string $object_name
     * @return ContentType
     */
    public function set_name($object_name)
    {
        $this->name = $object_name;

        return $this;
    }

    /**
     * Get object name
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Set object options
     *
     * @param  array $options
     * @return ContentType
     */
    public function set_options($options)
    {
        foreach ($options as $option_name => $value) {
            $method = 'set_' . $option_name;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * Set the object's singular label
     *
     * @param  string $object_single_name
     * @return ContentType
     */
    public function set_singlename($object_single_name)
    {
        $this->label_single = $object_single_name;

        return $this;
    }

    /**
     * Set the object's plural label
     *
     * @param  string $object_pluralname
     * @return ContentType
     */
    public function set_pluralname($object_pluralname)
    {

        $this->label_plural = $object_pluralname;

        return $this;
    }


    /**
     * Set the text domain from the plugin
     *
     * @param string $textdomain
     *
     * @return $this
     */
    public function set_textdomain($textdomain)
    {
        $this->textdomain = $textdomain;

        return $this;
    }

    /**
     * Set object arguments
     *
     * @param  array $args
     * @return ContentType
     */
    public function set_arguments($args = array())
    {
        $this->args = $args;

        return $this;
    }

}
