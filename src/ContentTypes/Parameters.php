<?php

namespace MangaPress\ContentTypes;

/**
 * Trait Parameters
 * @package MangaPress\ContentTypes
 */
trait Parameters
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
     * Objects that support this taxonomy (if ContentType is a taxonomy)
     *
     * @var array
     */
    protected $object_types = [];

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

    public function init()
    {
        add_action('init', [$this, 'register_content_type']);
    }

    /**
     * Set the object name
     *
     * @param string $object_name
     * @return Parameters
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
     * @param array $options
     * @return $this
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
     * @param string $single
     * @return $this
     */
    public function set_label_single($single)
    {
        $this->label_single = $single;

        return $this;
    }

    /**
     * Set the object's plural label
     *
     * @param string $plural
     * @return $this
     */
    public function set_label_plural($plural)
    {

        $this->label_plural = $plural;

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
}
