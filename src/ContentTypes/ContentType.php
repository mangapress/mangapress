<?php
/**
 * ContentType interface. Provides methods necessary for setting up a content-type
 *
 * @package MangaPress\ContentTypes
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\ContentTypes;

interface ContentType
{
    public function register_content_type();

    public function set_arguments($args = []);
}
