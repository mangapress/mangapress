<?php
/**
 * @package MangaPress\Theme\Traits
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

namespace MangaPress\Theme\Traits;

use MangaPress\Options\Options;

/**
 * Trait Markup
 * @package MangaPress\Theme\Traits
 */
trait Markup
{
    /**
     * @var string
     */
    private $theme;

    /**
     * Markup constructor.
     * Run default settings when object is created
     */
    public function __construct()
    {
        $this->theme = get_template();

        $latest_comic_page_exists = Options::get_option('latestcomic_page', 'basic');
        if ($latest_comic_page_exists) {
            add_action('mangapress_before_latest_comic_loop', '\MangaPress\Theme\Functions\start_latest_comic');
            add_action('mangapress_after_latest_comic_loop', '\MangaPress\Theme\Functions\end_latest_comic');
        }
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);

        $template_tags_file = MP_ABSPATH . 'resources/helpers/theme-compatibility/' . $this->theme . '-template-tags.php';
        if (file_exists($template_tags_file)) {
            require $template_tags_file;
        }
    }

    /**
     * Enqueue Manga+Press-specific stylesheet
     */
    public function enqueue_styles()
    {
        $theme = $this->get_theme_name();
        wp_register_style(
            "mangapress-{$theme}",
            MP_URLPATH . "resources/assets/css/{$theme}.css",
            null,
            MP_VERSION
        );

        wp_enqueue_style("mangapress-{$theme}");
    }

    /**
     * Get the theme-name set on creation
     * @return string
     */
    public function get_theme_name()
    {
        return $this->theme;
    }
}
