<?php
/**
 * All deprecated functions, actions, and filters.
 * With WP_DEBUG enabled, should produce warnings that these functions no longer exist
 * and alternatives should be used.
 */

/**
 * Get all comics for archives page
 * @deprecated Since 4.0.0
 * @since 2.9
 */
function mangapress_get_all_comics_for_archive($params = array())
{
    _deprecated_function(__FUNCTION__, MP_VERSION);
}

if (!function_exists('is_comic_page')) {
    /**
     * @since 1.0 RC1
     *
     * @global WP_Query $wp_query
     * @return bool
     */
    function is_comic_page()
    {
        _deprecated_function(__FUNCTION__ . ' is deprecated', MP_VERSION, 'is_latest_comic_page');
        return is_latest_comic_page();
    }
}

function mangapress_start_latest_comic()
{
    _deprecated_function(__FUNCTION__, MP_VERSION, __('See Manga+Press documentation'));
}

function mangapress_end_latest_comic()
{
    _deprecated_function(__FUNCTION__, MP_VERSION, __('See Manga+Press documentation'));
}

add_filter('the_latest_comic_content_error', function($error) {
    $msg = __('This filter has been deprecated. Please see documentation for alternatives', MP_DOMAIN);
    _deprecated_hook('the_latest_comic_content_error', MP_VERSION, $msg);
});

add_filter('the_comicarchive_content_error', function ($error) {
    $msg = __('This filter has been deprecated. Please see documentation for alternatives', MP_DOMAIN);
    _deprecated_hook('the_latest_comic_content_error', MP_VERSION, $msg);
});

add_action('latest_comic_start', function(){
    $msg = __('This action has been deprecated. Please see plugin documentation.', MP_DOMAIN);
    _deprecated_hook(
        'latest_comic_start',
        MP_VERSION,
        'mangapress_before_latest_comic or mangapress_before_latest_comic_loop',
        $msg
    );
});

add_action('latest_comic_end', function(){
    $msg = __('This action has been deprecated. Please see plugin documentation.', MP_DOMAIN);
    _deprecated_hook(
        'latest_comic_end',
        MP_VERSION,
        'mangapress_after_latest_comic or mangapress_after_latest_comic_loop',
        $msg
    );
});
