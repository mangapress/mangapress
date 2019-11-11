<?php
/**
 * ContentType Registry interface. Provides methods necessary for registering a content-type
 *
 * @package MangaPress\ContentTypes
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\ContentTypes;

interface ContentTypeRegistry
{
    public function register_content_types();
}