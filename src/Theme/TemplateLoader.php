<?php


namespace MangaPress\Theme;

use MangaPress\PluginComponent;

/**
 * Class TemplateLoader
 * @package MangaPress\Theme
 */
class TemplateLoader implements PluginComponent
{
    public function init()
    {
        add_filter('template_include', [$this, 'template_loader']);
    }

    public function template_loader()
    {
        //
    }
}
