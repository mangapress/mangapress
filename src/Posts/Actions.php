<?php


namespace MangaPress\Posts;

class Actions
{
    /**
     * Get image html
     *
     * @var string
     */
    const ACTION_INSERT_COMIC = 'mangapress-get-image-html';

    /**
     * Remove image html and return Add Image string
     *
     * @var string
     */
    const ACTION_REMOVE_COMIC = 'mangapress-remove-image';

    /**
     * Get cover image html
     */
    const ACTION_INSERT_COVER = 'mangapress-insert-cover';

    /**
     * Remove image html and return Add Cover string
     */
    const ACTION_REMOVE_COVER = 'mangapress-remove-cover';

    /**
     * Nonce string for Insert Comic
     *
     * @var string
     */
    const NONCE_INSERT_COMIC = self::ACTION_INSERT_COMIC;

    /**
     * Nonce string for Insert Comic
     *
     * @var string
     */
    const NONCE_REMOVE_COMIC = self::ACTION_REMOVE_COMIC;

    /**
     * Nonce string for Insert Comic
     *
     * @var string
     */
    const NONCE_INSERT_COVER = self::ACTION_INSERT_COVER;

    /**
     * Nonce string for Insert Comic
     *
     * @var string
     */
    const NONCE_REMOVE_COVER = self::ACTION_REMOVE_COVER;

}