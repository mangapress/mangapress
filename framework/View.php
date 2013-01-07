<?php
require_once 'View/ViewHelper.php';
/**
 * Description of View
 *
 * @author Jessica
 */
class View extends ViewHelper
{
    
    /**
     * WordPress Screen name, ex. post.php, edit.php. Can
     * be an array for multiple screens
     * 
     * @var string|array
     */
    protected $_hook = array();
    
    /**
     * Sanitized short-name. Usually post-type or taxonomy.
     * 
     * @var string
     */
    protected $_name = "";
    
    /**
     * Post-type that is to be used for enqueuing. Can be
     * an array for multiple post-types
     * 
     * @var string|array
     */
    protected $_post_type = array();
    
    /**
     * Array of stylesheet handles registered by wp_register_style()
     * 
     * @var array
     */
    protected $_styles = array();

    /**
     * Array of script handles registered by wp_register_script()
     * 
     * @var array
     */
    protected $_scripts = array();
    
    /**
     * Path to scripts/styles
     * 
     * @var string
     */
    protected $_path;
    
    /**
     * Script/style version #
     * 
     * @var string 
     */
    protected $_ver = '1.0'; // default version number

    public function init()
    {
        // set up default styles arrays
        $default_edit_styles = array(
            $this->_path . "css/{$this->_name}-edit-screen.css",
            $this->_path . "modules" . ucwords($this->_name)
                         . "/css/{$this->_name}-edit-screen.css",
            $this->_path . "css/edit-screen.css",
            "/framework/css/edit-screen.css",
        );
        
        $default_post_styles = array(
            $this->_path . "css/{$this->_name}-post-screen.css",
            $this->_path . "modules" . ucwords($this->_name)
                         . "/css/{$this->_name}-post-screen.css",
            $this->_path . "css/post-screen.css",                                 
            "/framework/css/post-screen.css",
        );

        wp_register_style(
            "{$this->_name}-edit-screen",
            $this->locate_stylesheet($default_edit_styles),
            null,
            $this->_ver,
            'screen'
        );

        wp_register_style(
            "{$this->_name}-post-screen",
            $this->locate_stylesheet($default_post_styles),
            null,
            $this->_ver,
            'screen'
        );                    
        
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_default_styles'));

    }

    /**
     * Set the relative path to the scripts/styles
     * 
     * @param string $path Path to scripts/styles
     * @return \View 
     */
    public function set_path($path)
    {
        $this->_path = $path;
        
        return $this;
    }
    
    /**
     * Set the hook that the scripts and styles are to be enqueued.
     * 
     * @param string $hook Screen hook name
     * @return \View
     */
    public function set_hook($hook)
    {
        $this->_hook = $hook;
        
        return $this;
    }
    
    public function get_hook()
    {
        return $this->_hook;
    }
    
    public function set_post_type($post_type)
    {
        $this->_post_type = $post_type;
        
        return $this;
    }

    public function set_ver($ver)
    {
        $this->_ver = $ver;
        
        return $this;
    }

    /**
     * Set JS files for enqueuing.
     *
     * @param array $scripts Array of JS script handles to be enqueued.
     * @return PostType_Class
     */
    public function set_js_scripts($scripts = array())
    {
        $this->_scripts = $scripts;

        return $this;
    }

    /**
     * Set CSS files for enqueuing.
     *
     * @param array $styles Array of CSS file handles to be enqueued.
     * @return PostType_Class
     */
    public function set_css_styles($styles = array())
    {
        $this->_styles = $styles;

        return $this;
    }
    
    /**
     * Enqueues all styles and scripts. Runs when admin_enqueue_scripts is
     * called.
     *
     * @global string $post_type
     * @global string $hook_suffix
     * @return void
     */
    public function enqueue_styles()
    {
        global $post_type, $hook_suffix;
        
        $is_post_type = $this->is_post_type($post_type);
        $is_screen = $this->is_screen_hook($hook_suffix);
        
        if (($is_post_type && $is_screen) || ($post_type == null) && $is_screen) {

            $scripts = $this->_styles;

            foreach ($scripts as $script) {
                wp_enqueue_style($script);
            }
        }
        
        return $this;
    }
    
    /**
     * Enqueues all scripts. Runs when admin_enqueue_scripts is
     * called.
     *
     * @global string $post_type
     * @global string $hook_suffix
     * @return void
     */
    public function enqueue_scripts()
    {
        global $post_type, $hook_suffix;        

        $is_post_type = $this->is_post_type($post_type);
        $is_screen = $this->is_screen_hook($hook_suffix);
        
        if (($is_post_type && $is_screen) || ($post_type == null) && $is_screen) {

            $scripts = $this->_scripts;

            foreach ($scripts as $script) {
                wp_enqueue_script($script);
            }
        }
        
        return $this;
    }
    
    /**
     * Enqueues default styles for Add New & Edit screens
     * 
     * @global string $post_type
     * @global string $hook_suffix
     * @return void
     */
    public function enqueue_default_styles()
    {
        global $post_type, $hook_suffix;
        
        $valid_suffices = array('post.php', 'post-new.php');
        
        if ($this->is_post_type($post_type) && in_array($hook_suffix, $valid_suffices) ) {
            wp_enqueue_style("{$this->_name}-post-screen");
        } else if ($this->is_post_type($post_type) && $hook_suffix == 'edit.php') {
            wp_enqueue_style("{$this->_name}-edit-screen");
        }

        return $this;
        
    }
    
}
?>