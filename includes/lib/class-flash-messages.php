<?php
/**
 * Manga+Press Flash Message class
 *
 * @package Manga_Press
 * @author Jess Green <jgreen @ psy-dreamer.com>
 * @version $Id$
 */

/**
 * Flash Messages helper class
 *
 * @package FlashMessages
 * @author Jess Green <jgreen @ psy-dreamer.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class MangaPress_FlashMessages
{


    /**
     * CSS classes. Filtered by flash_message_classes filter
     *
     * @var array
     */
    protected $classes = array('error', 'updated');


    /**
     * Default messages
     * @var array
     */
    public $messages = array();


    /**
     * Name of transient that stores messages
     * @var string
     */
    protected $transient_name = '';


    /**
     * PHP5 Constructor function
     *
     * @param array $options Set properties when class is initialized
     */
    public function __construct($options)
    {
        foreach ($options as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        add_action('admin_notices', array($this, 'show_flash_message'));
    }


    /**
     * End session on logout and login
     *
     * @return void
     */
    public function session_end()
    {
        delete_transient($this->get_transient_name());
    }


    /**
     * Set messages in array
     *
     * @param array $messages Array of messages to set
     * @return void
     */
    public function set_flash_messages($messages)
    {
        $this->messages = $messages;
    }


    /**
     * Set transient name
     *
     * @param string $transient_name
     * @return void
     */
    public function set_transient_name($transient_name)
    {
        $this->transient_name = $transient_name;
    }


    /**
     * Get the transient name. Throw exception if not set
     *
     * @return string
     * @throws Exception
     */
    public function get_transient_name()
    {
        if ($this->transient_name == '') {
            throw new Exception("Transient name is not set");
        }

        return $this->transient_name;
    }


    /**
     * Queue flash messages
     *
     * @param string $name Name of message. updated or error
     * @param mixed $message Message body
     * @return FlashMessages
     */
    public function queue_flash_message($name, $message)
    {
        $messages = array();
        $classes       = apply_filters('flashmessage_classes', $this->classes);
        $default_class = apply_filters('flashmessages_default_class', 'updated');

        $class = $name;
        if (!in_array($name, $classes)) {
            $class = $default_class;
        }

        $messages[$class]['message'] = maybe_serialize($message);

        set_transient($this->get_transient_name(), $messages);

        return $this;
    }


    /**
     * Get flash message
     *
     * @return mixed
     */
    public function show_flash_message()
    {
        $messages = get_transient($this->get_transient_name());
        if (is_array($messages)) {
            foreach ($messages as $class => $messages) {
                $this->display_flash_message_html($messages, $class);
            }
        }

        $this->session_end();
    }


    /**
     * Display message HTML
     *
     * @param array $messages Array of messages
     * @param string $class Message CSS class
     * @return void
     */
    private function display_flash_message_html($messages, $class)
    {
        foreach ($messages as $message_raw) {
            $message = maybe_unserialize($message_raw);
            $message_html = '';
            if (is_array($message)) {
                if ($message['id'] == filter_input(INPUT_GET, 'post')) {
                    $message_html = "<div id=\"message\" class=\"{$class}\"><p>{$message['message']}</p></div>";
                }
            } else {
                $message_html = "<div id=\"message\" class=\"{$class}\"><p>{$message}</p></div>";
            }

            echo apply_filters('flashmessage_html', $message_html, $message, $class);
        }
    }

}
