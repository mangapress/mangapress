<?php


namespace MangaPress\Options\Fields;

/**
 * Class Field
 * @package MangaPress\Options\Fields
 */
class Field
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
     * The element data-type. Determines validation
     *
     * @var string
     */
    protected $data_type;

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
     * Set options on object init.
     *
     * @param array $options
     * @return void
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
     * Add attributes to element
     *
     * @param array $attributes
     * @return $this
     */
    public function add_attributes(array $attributes = [])
    {
        $this->set_attributes($attributes);

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
     * @return $this
     */
    public function set_attributes($attr)
    {
        foreach ($attr as $key => $value) {
            $this->attr[$key] = $value;
        }

        return $this;
    }

    /**
     * Set label
     *
     * @param string $text
     * @return $this
     */
    public function set_label($text = '')
    {
        $this->label = $text;

        return $this;
    }

    /**
     * Set default value
     *
     * @param mixed $default
     * @return $this
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
     * Set the data type
     *
     * @param string $data_type
     * @return $this
     */
    public function set_data_type($data_type)
    {
        $this->data_type = $data_type;

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
     * @return $this
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
        if ($this->description) {
            return sprintf('<span class="description">%s</span>', $this->description);
        }

        return $this->description;
    }

    /**
     * Build the attribute string
     *
     * @return string
     */
    public function build_attr_string()
    {
        $attr_arr = [];
        foreach ($this->attr as $name => $value) {
            $attr_arr[] = "{$name}=\"{$value}\"";
        }
        $attr = implode(" ", $attr_arr);

        return $attr;
    }
}
