<?php

abstract class FrameWork_Helper
{
    public $name;
    public $label_single;
    public $label_plural;
    
    protected $_name;
    protected $_label_single;
    protected $_label_plural;
    protected $_args;

    abstract public function init();
    
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->set_options($options)
                 ->init();
        }
    }
    
    public function set_name($object_name)
    {
        $this->name = $this->_name = $object_name;
        
        return $this;
    }
    
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
    
    public function set_singlename($object_single_name)
    {
        $this->label_single = $this->_label_single = $object_single_name;
        
        return $this;
    }
    
    public function set_pluralname($object_pluralname)
    {

        $this->label_plural = $this->_label_plural = $object_pluralname;
        
        return $this;
    }
    
    public function set_arguments($args)
    {
        $this->_args = $args;
        
        return $this;
    }
    
}
?>