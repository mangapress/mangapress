<?php
/**
 * MangaPress
 *
 * @package MangaPress\Templates\Archive_Comic
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

/** This action is documented in resources/templates/single-comic.php **/
do_action('mangapress_get_comic_header');

/** This filter is documented in templates/single-comic.php */
do_action('mangapress_before_content');

/**
 * mangapress_comic_archive_header
 *
 * Output Comic Archive page header
 */
do_action('mangapress_comic_archive_header');

if ((comic_archive_is_gallery() || comic_archive_is_list())) {
    while (have_posts()) :
        the_post();

        /**
         * mangapress_archive_page_opening_tag
         *
         * Output the opening wrapping tag based on theme tags
         * @param string $tag Opening tag, defaults to 'article'
         * @since 4.0.0
         *
         */
        do_action('mangapress_archive_page_opening_tag', 'article');


        /**
         * mangapress_before_article
         *
         * Run scripts or insert content before the article tag but after the loop starts
         * @since 4.0.0
         */
        do_action('mangapress_before_article');

        /**
         * mangapress_article_header
         *
         * Add post header
         * @since 4.0.0
         */
        do_action('mangapress_article_header', $post);

        $archive_query = new \WP_Query(
            [
                'post_type'      => \MangaPress\Posts\Comics::POST_TYPE,
                'post_status'    => 'publish',
                'order'          => \MangaPress\Options\Options::get_option('archive_order', 'basic'),
                'orderby'        => \MangaPress\Options\Options::get_option('archive_orderby', 'basic'),
                'posts_per_page' => -1,
            ]
        );

        if ($archive_query->have_posts()) {

            /**
             * mangapress_archive_style_opening_tag
             *
             * Output the opening wrapping tag based on the archive style
             * @param string $archive_style
             * @since 4.0.0
             *
             */
            do_action('mangapress_archive_style_opening_tag', mangapress_get_comic_archive_style());

            /**
             * mangapress_before_archive_comic_loop
             *
             * Run scripts or insert content directly before latest comic loop
             * @since 4.0.0
             */
            do_action('mangapress_before_archive_comic_loop');

            while ($archive_query->have_posts()) :
                $archive_query->the_post();

                /**
                 * mangapress_opening_article_tag
                 *
                 * Filter and then output the article tag
                 * @param string $archive_style
                 * @param array $args {
                 *      Array of accepted arguments
                 * @type string $style
                 * }
                 * @return string
                 * @since 4.0.0
                 *
                 */
                echo apply_filters(
                    'mangapress_opening_article_tag',
                    'article',
                    ['style' => mangapress_get_comic_archive_style()]
                );

                /**
                 * mangapress_article_header
                 *
                 * Add post header
                 * @since 4.0.0
                 */
                do_action('mangapress_article_header', $post);

                /**
                 * mangapress_before_article_content
                 *
                 * Run scripts or insert content before the article content but after the article opening tag
                 * @since 4.0.0
                 */
                do_action('mangapress_before_article_content');

                /**
                 * mangapress_archive_style_template
                 *
                 * Output the individual archive entry markup based on archive style
                 * @param string $archive_style
                 * @since 4.0.0
                 *
                 */
                do_action('mangapress_archive_style_template', mangapress_get_comic_archive_style());

                /**
                 * mangapress_after_article_content
                 *
                 * Run scripts or insert content after the article content but before the article closing tag
                 * @since 4.0.0
                 */
                do_action('mangapress_after_article_content');

                /**
                 * mangapress_article_footer
                 *
                 * Output article footer
                 * @since 4.0.0
                 */
                do_action('mangapress_article_footer');

                /**
                 * mangapress_closing_article_tag
                 *
                 * Filter and then output the closing article tag
                 * @param string $archive_style
                 * @param array $args {
                 *      Array of accepted arguments
                 * @type string $style
                 * }
                 * @since 4.0.0
                 *
                 */
                echo apply_filters(
                    'mangapress_closing_article_tag',
                    'article',
                    ['style' => mangapress_get_comic_archive_style()]
                );
            endwhile;

            wp_reset_query();

            /**
             * mangapress_archive_style_closing_tag
             *
             * Output the closing wrapping tag based on the archive style
             * @param string $archive_style
             * @since 4.0.0
             *
             */
            do_action('mangapress_archive_style_closing_tag', mangapress_get_comic_archive_style());

            /**
             * mangapress_after_archive_comic_loop
             *
             * Run scripts or insert content directly after comic archive loop
             * @since 4.0.0
             */
            do_action('mangapress_after_archive_comic_loop');
        } else {

            /**
             * mangapress_output_no_comics_message
             *
             * Outputs message if no comics are found for archive or other listings
             * @since 4.0.0
             */
            echo apply_filters(
                'mangapress_output_no_comics_message',
                '<div class="entry-content"><p>' . __('No comics found', MP_DOMAIN) . '</p></div>'
            );
        }

        /**
         * mangapress_article_footer
         *
         * Output article footer
         * @since 4.0.0
         */
        do_action('mangapress_article_footer', $post);

        /**
         * mangapress_after_article
         *
         * Run scripts or insert content after the closing article tag
         * but before the main loop ends or iterates to the next post
         * @since 4.0.0
         */
        do_action('mangapress_after_article');

        /**
         * mangapress_archive_page_closing_tag
         *
         * Output the closing wrapping tag based on theme
         * @param string $tag Closing tag, defaults to article
         * @since 4.0.0
         *
         */
        do_action('mangapress_archive_page_closing_tag', 'article');

    endwhile;
} else {

    /**
     * mangapress_archive_page_opening_tag
     *
     * Output the opening wrapping tag based on theme tags
     * @param string $tag Opening tag, defaults to 'article'
     * @since 4.0.0
     *
     */
    do_action('mangapress_archive_page_opening_tag', 'article');

    /**
     * @global wpdb $wpdb WordPress DB object
     */
    global $wpdb;

    $years = $wpdb->get_col(
        "SELECT DISTINCT YEAR(post_date) as year FROM {$wpdb->posts} 
            WHERE 1=1 
                AND post_type='mangapress_comic'
                AND post_status='publish'    
            ORDER BY post_date DESC"
    );

    foreach ($years as $year) {
        for ($i = 12; $i > 1; $i--) {
            mangapress_get_calendar($i, $year, false, true);
        }
    }


    /**
     * mangapress_archive_page_closing_tag
     *
     * Output the closing wrapping tag based on theme
     * @param string $tag Closing tag, defaults to article
     * @since 4.0.0
     *
     */
    do_action('mangapress_archive_page_closing_tag', 'article');
}

/** This filter is documented in resources/templates/single-comic.php */
do_action('mangapress_after_content');

/** This filter is documented in resources/templates/single-comic.php */
do_action('mangapress_sidebar');

/**
 * mangapress_before_footer
 *
 * Handle output before footer
 * @since 4.0.0
 */
do_action('mangapress_before_footer');

/** This action is documented in resources/templates/single-comic.php **/
do_action('mangapress_get_comic_footer');
