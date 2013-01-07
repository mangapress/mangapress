<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ViewHelper
 *
 * @author Jessica
 */
abstract class ViewHelper
{
    
    abstract public function init();
    
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->set_options($options)
                 ->init();
        }
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
    
    public function is_post_type($post_type)
    {
        if (is_array($this->_post_type)) {
            return in_array($post_type, $this->_post_type);
        } else if (is_string($this->_post_type)) {
            return ($this->_post_type == $post_type);
        } else if ($post_type == null) {
            return null;
        }
        
        return false;
    }
    
    public function is_screen_hook($hook)
    {
        if (is_array($this->_hook)) {
            return in_array($hook, $this->_hook);
        } else if (is_string($this->_hook)) {
            return ($this->_hook == $hook);
        } else {
            return false;
        }
    }
    
    public function locate_stylesheet($style_sheets)
    {
        $located = '';
        foreach ( (array) $style_sheets as $style_sheet ) {
            if ( !$style_sheet )
                    continue;
            if ( file_exists( STYLESHEETPATH . $style_sheet)) {
                    $located = get_template_directory_uri() . $style_sheet;
                    break;
            }
        }

        if ( '' != $located ) {
            return $located;
        }
        
        return false;
    }

    
}
?>