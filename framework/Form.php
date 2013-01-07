<?php
include('Form/Element.php');
include('Form/Element/Text.php');
include('Form/Element/Textarea.php');
include('Form/Element/Select.php');
include('Form/Element/Radio.php');
include('Form/Element/Checkbox.php');
include('Form/Element/Button.php');

class Form
{
    
    public $_form_elements = array();

    public $_form_properties;

    public $form;

    public function __construct()
    {
    }

    public function set_properties(array $properties = array())
    {
        $this->_form_properties = $properties;

        return $this;
    }

    public function get_properties()
    {
        return $this->_form_properties;
    }

    public function add_element($element, $name = null, $options = array())
    {
        if (is_string($element)) {
            if ($name === null) {
                // Change this to throw custom exception
                return new WP_Error('name_is_null', __('Name cannot be null.'));
            }
            
            $this->_form_elements[$name] = $this->createElement($element, $name, $this->_form_properties);

        } elseif (is_object($element)) {
            
            if ($name === null) {
                $name = $element->getAttributes('name');
            }

            $this->_form_elements[$name] = $this->createElement($element, $name, $this->_form_properties);
        }
        
        return $this->_form_elements[$name];
    }

    public function create_element($type, $name, $options = array())
    {

        if (is_string($type)) {
            $class = 'WP_' . ucfirst($type);
            $element = new $class($name, $options);
            
        } elseif (is_object($type)) {
            $element = $type;
        }

        return $element;
    }
    
    public function __toString()
    {
        $form = "";
        foreach ($this->_form_elements as $element) {
            $form .= $element;
        }
        
        return $form;
    }
}
?>