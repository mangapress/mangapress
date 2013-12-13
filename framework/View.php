<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */

/**
 * MangaPress_View
 * View class for handling frontend-related functionality, ex. JS/CSS enqueue
 * 
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress_View
 * @version $Id$
 */
class MangaPress_View
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

    /**
     * Handle stylesheet/script registration
     *
     * @return void
     */

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
                "{$this->_name}-edit-screen", $this->locate_stylesheet($default_edit_styles), null, $this->_ver, 'screen'
        );

        wp_register_style(
                "{$this->_name}-post-screen", $this->locate_stylesheet($default_post_styles), null, $this->_ver, 'screen'
        );

        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_default_styles'));
    }

    /**
     * PHP5 Constructor function. Use to set options.
     *
     * @param array $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->set_options($options)
                    ->init();
        }
    }

    /**
     * Options setter. Calls setter methods if a corresponding
     * method exists for option.
     *
     * @param array $options
     * @return \MangaPress_View
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
     * Checks if the string passed matches the post-type set by set_posttype
     *
     * @param string $post_type
     * @return boolean
     */
    public function is_post_type($post_type)
    {
        if (is_array($this->_post_type)) {
            return in_array($post_type, $this->_post_type);
        } else if (is_string($this->_post_type)) {
            return ($this->_post_type == $post_type);
        } else if ($post_type == null) {
            return false;
        }

        return false;
    }

    /**
     * Checks if the screen hook passed by $hook is the same
     * hook specified by $this->_hook
     *
     * @param string $hook
     * @return boolean
     */
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

    /**
     * Looks for a group of CSS files in matching path
     *
     * @param array $style_sheets Array of CSS files to look for
     * @return string|boolean
     */
    public function locate_stylesheet($style_sheets)
    {
        $located = '';
        foreach ((array) $style_sheets as $style_sheet) {
            if (!$style_sheet)
                continue;
            if (file_exists(STYLESHEETPATH . $style_sheet)) {
                $located = get_template_directory_uri() . $style_sheet;
                break;
            }
        }

        if ('' != $located) {
            return $located;
        }

        return false;
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
     * @return \MangaPress_View
     */
    public function set_hook($hook)
    {
        $this->_hook = $hook;

        return $this;
    }

    /**
     * Get hook name
     *
     * @return string
     */
    public function get_hook()
    {
        return $this->_hook;
    }

    /**
     * Set the post-type
     *
     * @param string $post_type
     * @return \MangaPress_View
     */
    public function set_post_type($post_type)
    {
        $this->_post_type = $post_type;

        return $this;
    }

    /**
     * Set version number for JS and CSS files. Prevents caching
     *
     * @param string $ver
     * @return \MangaPress_View
     */
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

        if ($this->is_post_type($post_type) && in_array($hook_suffix, $valid_suffices)) {
            wp_enqueue_style("{$this->_name}-post-screen");
        } else if ($this->is_post_type($post_type) && $hook_suffix == 'edit.php') {
            wp_enqueue_style("{$this->_name}-edit-screen");
        }

        return $this;
    }

}
