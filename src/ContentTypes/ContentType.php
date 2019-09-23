<?php


namespace MangaPress\ContentTypes;

interface ContentType
{
    public function init();

    public function register_content_type();

    public function set_arguments($args = []);
}
