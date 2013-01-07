<?php
/**
 * @package WordPress
 * @subpackage Options
 * @author Jess Green <jgreen@nerdery.com>
 * @version $Id$
 */

/**
 * options
 * Created Jan 30, 2012 @ 9:23:16 AM
 *
 * @author Jess Green <jgreen@nerdery.com>
 */
abstract class Options extends FrameWork_Helper
{

    /**
     * Option group name. Used as html field name for option output.
     *
     * @var string
     */
    protected $_options_group;

    /**
     * Array of available option fields.
     *
     * @var array
     */
    protected $_option_fields;

    /**
     * Array of option sections.
     *
     * @var array
     */
    protected $_option_sections;

    /**
     * Option page hook
     *
     * @var string
     */
    protected $_option_page;

    /**
     * PHP5 constructor function
     *
     * @return void
     */
    public function __construct($args = array())
    {
        $name = $args['name'];

        if (is_array($args)) {
            $this->set_options($args);
        }

        add_action("{$name}_option_fields", array($this, 'set_options_field'), 10, 1);
        add_action("{$name}_option_section", array($this, 'set_section'), 10, 1);
        add_action('admin_init', array($this, 'options_init'));

    }

    /**
     * Register settings, sections and fields
     *
     * @return void
     */
    public function options_init()
    {

        if (defined('DOING_AJAX') && DOING_AJAX)
            return;
        
        /*
         * register_setting()
         * Settings should be stored as an array in the options table to
         * limit the number of queries made to the DB. The option name should
         * be the same as the option group.
         *
         * Using the options group in a page registered with add_options_page():
         * settings_fields($my_options_class->get_optiongroup_name())
         */
        register_setting(
            $this->_options_group,
            $this->_options_group,
            array($this, 'sanitize_options')
        );

        $sections = $this->get_sections();
        foreach ($sections as $section_name => $data) {
            add_settings_section(
                "{$this->_options_group}-{$section_name}",
                $data['title'],
                array($this, 'settings_section_cb'),
                "{$this->_options_group}-{$section_name}"
            );
        }

        $this->output_settings_fields();
    }

    public function output_settings_fields()
    {
        
        $field_sections = $this->_option_fields;

        foreach ($field_sections as $field_section => $field) {
            foreach ($field as $field_name => $field_data) {
                add_settings_field(
                    "{$field_section}_options-{$field_data['id']}",
                    (isset($field_data['title']) ? $field_data['title'] : " "),
                    $field_data['callback'],
                    "{$this->_options_group}-{$section_name}",
                    "{$this->_options_group}-{$section_name}",
                    array_merge(array('name' => $field_name), $field_data)
                );
            }
        }

    }

    /**
     * Sets the options
     *
     * @param type $options
     * @return \Options
     */
    public function set_optiongroup_name($options)
    {
        $this->_options_group = $options;

        return $this;
    }

    /**
     * Returns the options group name
     *
     * @return array|\WP_Error
     */
    public function get_optiongroup_name()
    {
        if ($this->_options_group == "") {
            return new WP_Error("no_option_name", "Option name is empty");
        }

        return $this->_options_group;
    }

    public function set_option_page($page)
    {
        $this->_option_page = $page;

        return $this;
    }

    /**
     * Returns the options group name
     *
     * @return array|\WP_Error
     */
    public function get_option_page()
    {
        if ($this->_option_page == "") {
            return new WP_Error("no_option_page", "Option page must be set.");
        }

        return $this->_option_page;
    }

    /**
     * Sets the options fields sections.
     *
     * @param type $sections
     * @return \Options
     */
    public function set_sections($sections)
    {
        $this->_option_sections = $sections;

        return $this;
    }

    /**
     * Returns the available options fields sections.
     *
     * @return array|\WP_Error
     */
    public function get_sections()
    {
        if ($this->_option_sections == "") {
            return new WP_Error("no_option_sections", "Sections not set.");
        }

        return $this->_option_sections;

    }

    /**
     * Adds a new section to available sections array. To be used by plugins
     * for modifying available
     *
     * @param array $section
     * @return array
     */
    public function set_section($section)
    {
        if (!is_array($section)) {
            return new WP_Error("section_not_array", "Section should be an array");
        }

        $this->_option_sections = array_merge($this->_option_sections, $section);

        return $this->_option_sections;
    }

    /**
     * Retrieves a section.
     *
     * @param string $section_name Name of section being retrieved.
     * @return array
     */
    public function get_a_section($section_name)
    {
        if (!isset($this->_option_sections[$section_name])) {
            return new WP_Error("no_section_exists", "Section does not exist.");
        }

        return $this->_option_sections[$section_name];
    }

    /**
     * Set option fields
     *
     * @param array $option Option field array
     * @return array|\WP_Error
     */
    public function set_options_field($option)
    {
        if (!is_array($option)) {
            return new WP_Error("option_not_array", "Options must be an array");
        }

        $this->_option_fields = $option;

        return $this->_option_fields;

    }

    /**
     * Returns an option field
     *
     * @param string $option_name Name of option to be returned
     * @return array|\WP_Error
     */
    public function get_options_field($option_name)
    {
        if (!isset($this->_option_fields[$option_name])) {
            return new WP_Error("no_option_exists", "Option does not exist.");
        }

        return $this->_option_fields[$option_name];

    }

    public function set_view($view)
    {
        $this->_view = $view;

        return $this;
    }

    public function get_view()
    {
        if (!($this->_view instanceof View)) {
            return new WP_Error('not_view', '$this->_view is not an instance of View');
        }

        return $this->_view;
    }

    public function init()
    {
        return $this;
    }

    /**
     * settings_section_cb()
     * Outputs Settings Sections
     *
     * @param string $section Name of section
     * @return void
     */
    public function settings_section_cb($section)
    {
        $options = $this->options_sections();

        $current = (substr($section['id'], strpos($section['id'], '-') + 1));

        echo "<p>{$options[$current]['description']}</p>";
    }

    /**
     * Set option fields
     *
     * @param $options Array of available options
     * @return array
     */
    abstract public function options_fields($options = array());

    /**
     * Output option fields
     *
     * @param mixed $option Current option to output
     * @return string
     */
    abstract public function settings_field_cb($option);

    /**
     *
     * @param array $options Array of options to be sanitized
     * @return array Sanitized options array
     */
    abstract public function sanitize_options($options);

}

?>