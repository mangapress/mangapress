<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @package MangaPress
 */
namespace MangaPress\Form;

/**
 * MangaPress_Element
 * Abstract class used to define basic functionality for extending classes
 *
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @package MangaPress_Element
 * @version $Id$
 */
class Element
{
    /**
     * Elements HTML attributes array
     *
     * @var array
     */
    protected $attr;

    /**
     * Element label text
     *
     * @var string
     */
    protected $label;

    /**
     * Name attribute
     *
     * @var string
     */
    protected $name;

    /**
     * Default value
     * @var mixed
     */
    protected $default_value;

    /**
     * Value (if different from default)
     *
     * @var mixed
     */
    protected $value;

    /**
     * Object html
     *
     * @var string
     */
    protected $html;

    /**
     * Description field
     *
     * @var string
     */
    protected $description;

    /**
     * PHP5 constructor method.
     *
     * @param array $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->set_options($options);
        }

    }

    /**
     * Set options
     *
     * @param array $options
     * @return \MangaPress\Form\Element
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
     * Add attributes to element
     *
     * @param array $attributes
     * @return \MangaPress\Form\Element
     */
    public function add_attributes(array $attributes = array())
    {
        foreach ($attributes as $attr => $value) {
            $this->set_attribute($attr, $value);
        }

        return $this;
    }

    /**
     * Get attributes as represented by $key
     *
     * @param string $key Attribute to retrieve
     * @return null|string
     */
    public function get_attributes($key)
    {
        if (!isset($this->attr[$key])) {
            return null;
        }

        return $this->attr[$key];
    }

    /**
     * Set attributes
     *
     * @param array $attr
     * @return \MangaPress\Form\Element
     */
    public function set_attributes($attr)
    {
        foreach ($attr as $key => $value){
            $this->attr[$key] = $value;
        }

        return $this;
    }

    /**
     * Set attributes
     *
     * @param string $attr
     * @param string $value
     * @return \MangaPress\Form\Element
     */
    public function set_attribute($attr, $value)
    {
        $this->attr[$attr] = $value;

        return $this;
    }

    /**
     * Set label
     *
     * @param string $text
     * @return \MangaPress\Form\Element
     */
    public function set_label($text = '') {

        $this->label = $text;

        return $this;
    }

    /**
     * Set default value
     *
     * @param mixed $default
     * @return \MangaPress\Form\Element
     */
    public function set_default($default)
    {
        $this->default_value = $default;

        return $this;
    }

    /**
     * Get default value
     *
     * @return mixed
     */
    public function get_default()
    {
        return $this->default_value;
    }

    /**
     * Get value attribute
     *
     * @return mixed
     */
    public function get_value()
    {
        return $this->attr['value'];
    }

    /**
     * Set value
     *
     * @param mixed $value
     * @return \MangaPress\Form\Element
     */
    public function set_value($value)
    {
        $this->attr['value'] = $value;

        return $this;
    }

    /**
     * Get name attribute
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_attributes('name');
    }

    /**
     * Set description
     *
     * @param string $description
     * @return \MangaPress\Form\Element
     */
    public function set_description($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     * Build the attribute string
     *
     * @return string
     */
    public function build_attr_string()
    {
        $attr_arr = array();
        foreach ($this->attr as $name => $value)
            $attr_arr[] = "{$name}=\"{$value}\"";

        $attr = implode(" ", $attr_arr);

        return $attr;
    }

}