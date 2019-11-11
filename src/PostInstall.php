<?php
/**
 * @package MangaPress
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress;

use MangaPress\Options\Options;
use MangaPress\Posts\ComicPages;
use MangaPress\Posts\Comics;

/**
 * Class PostInstall
 * @package MangaPress
 */
class PostInstall implements PluginComponent
{
    /**
     * Check for option, if true then add to init
     */
    public function init()
    {
        $run_post_activation = get_option('mangapress_post_activation', false);
        if ($run_post_activation) {
            add_action('init', [$this, 'post_activation_tasks'], 500);
        }
    }

    /**
     * Run post-activation tasks
     */
    public function post_activation_tasks()
    {
        if (!get_option('mangapress_default_category', false)) {
            // create a default series category
            $term = wp_insert_term(
                'Default Series',
                Comics::TAX_SERIES,
                [
                    'description' => __(
                        'Default Series category created when plugin is activated. '
                        . 'It is suggested that you rename this category.',
                        MP_DOMAIN
                    ),
                    'slug'        => 'default-series',
                ]
            );

            if (!($term instanceof \WP_Error)) {
                add_option('mangapress_default_category', $term['term_id'], '', 'no');
            }
        }

        $latest_created = false;
        if (!Options::get_option('comicarchive_page', 'basic')) {
            // create latest comic and comic archive posts
            $params = [
                'post_type'   => ComicPages::POST_TYPE,
                'post_title'  => 'Comic Archives',
                'post_name'   => 'comic-archives',
                'post_status' => 'draft',
            ];

            $archive = wp_insert_post($params);

            if (!is_wp_error($archive)) {
                Options::set_option('comicarchive_page', $archive, 'basic');
                $latest_created = true;
            }
        }

        $archive_created = false;
        if (!Options::get_option('latestcomic_page', 'basic')) {
            $params = [
                'post_type'   => ComicPages::POST_TYPE,
                'post_title'  => 'Latest Comic',
                'post_name'   => 'latest-comic',
                'post_status' => 'draft',
            ];

            $latest = wp_insert_post($params);

            if (!is_wp_error($latest)) {
                Options::set_option('latestcomic_page', $latest, 'basic');
                $archive_created = true;
            }
        }

        if ($archive_created || $latest_created) {
            Options::save_options();
        }

        delete_option('mangapress_post_activation');
    }
}