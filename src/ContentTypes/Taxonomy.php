<?php


namespace MangaPress\ContentTypes;

/**
 * Class Taxonomy
 * @package MangaPress\ContentTypes
 */
class Taxonomy implements ContentType
{
    use Parameters;

    public function register_content_type()
    {
        register_taxonomy($this->name, $this->object_types, $this->args);
    }

    public function set_arguments($args = [])
    {
        // TODO: Implement set_arguments() method.
    }
}
