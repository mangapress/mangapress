<?php
/**
 * Simple Archive Calendar template
 * @package Manga_Press
 */
for ($i = 1 ; $i <= 12 ; $i++) {
    mangapress_get_calendar($i, 2015, false);
}
