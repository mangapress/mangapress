<?php


namespace MangaPress\Widgets;

use MangaPress\PluginComponent;

/**
 * Class WidgetsRegistry
 * @package MangaPress\Widgets
 */
class WidgetsRegistry implements PluginComponent
{
    protected $registered_widgets = [];

    public function __construct($widgets = [])
    {
        foreach ($widgets as $widget) {
            $this->registered_widgets[] = $widget;
        }
    }

    public function init()
    {
        add_action('widgets_init', [$this, 'widgets_init']);
    }

    public function widgets_init()
    {
        if (!empty($this->registered_widgets)) {
            foreach ($this->registered_widgets as $widget) {
                register_widget(__NAMESPACE__ . '\\' . $widget);
            }
        }
    }
}
