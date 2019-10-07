<?php


namespace MangaPress\Options\Fields\Types;

class PageSelect extends Select
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $pages   = get_pages();
        $options = array_merge([], ['no_val' => __('Select a Page', MP_DOMAIN)]);
        foreach ($pages as $page) {
            $options[$page->post_name] = $page->post_title;
        }

        $this->options = $options;
    }
}