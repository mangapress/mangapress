<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */

/**
 * MangaPress_FrameWork_Helper
 * Abstract class used to define basic functionality for extending classes
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress_View
 * @version $Id$
 */
abstract class MangaPress_FrameWork_Helper
{

    /**
     * Object name (post-type or taxonomy)
     * @var string
     */
    protected $_name;

    /**
     * Human-readable singular name
     *
     * @var string
     */
    protected $_label_single;

    /**
     * Human-readable plural name
     *
     * @var string
     */
    protected $_label_plural;

    /**
     * Object arguments
     * @var array
     */
    protected $_args;

    /**
     * Init method
     */
    abstract public function init();

    /**
     * Set arguments method
     */
    abstract public function set_arguments($args = array());

    /**
     * PHP5 constructor method
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
     * Set object name
     *
     * @param string $object_name
     * @return \MangaPress_FrameWork_Helper
     */
    public function set_name($object_name)
    {
        $this->_name = $object_name;

        return $this;
    }

    /**
     * Get object name
     * 
     * @return string
     */
    public function get_name()
    {
        return $this->_name;
    }

    /**
     * Set options
     *
     * @param array $options
     * @return \MangaPress_FrameWork_Helper
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
     * Set human-readable singular name
     *
     * @param string $object_single_name
     * @return \MangaPress_FrameWork_Helper
     */
    public function set_singlename($object_single_name)
    {
        $this->_label_single = $object_single_name;

        return $this;
    }

    /**
     * Set human-readable plural name
     *
     * @param string $object_pluralname
     * @return \MangaPress_FrameWork_Helper
     */
    public function set_pluralname($object_pluralname)
    {

        $this->_label_plural = $object_pluralname;

        return $this;
    }

}
