<?php
/**
 * General functions used for building themes
 */
if (!function_exists('is_comic')) {
    /**
     * is_comic()
     * Used to detect if post is a comic post
     * @return bool Returns true if post contains a comic, false if not.
     * @see \is_singular()
     * @since 0.1
     */
    function is_comic()
    {
        return is_singular(\MangaPress\Posts\Comics::POST_TYPE);
    }
}

if (!function_exists('is_latest_comic_page')) {
    /**
     * @return bool
     * @since 4.0.0
     *
     */
    function is_latest_comic_page()
    {
        // check for latest-comic query var
        if (is_latest_comic_endpoint()) {
            return false;
        }

        $latest_comic_page = \MangaPress\Options\Options::get_option('latestcomic_page', 'basic');
        if (empty($latest_comic_page)) {
            return false;
        }

        return is_page($latest_comic_page);
    }
}

if (!function_exists('is_latest_comic_endpoint')) {
    /**
     * @return bool
     * @since 4.0.0
     * @global WP_Query $wp_query
     *
     */
    function is_latest_comic_endpoint()
    {
        global $wp_query;
        return isset($wp_query->query['latest-comic']);
    }
}

if (!function_exists('is_comic_archive_page')) {
    /**
     * Are we on an archive page for the comic post-type
     * @return bool
     * @global WP_Query $wp_query
     * @since 1.0 RC1
     *
     */
    function is_comic_archive_page()
    {
        global $wp_query;

        return $wp_query->is_post_type_archive(\MangaPress\Posts\Comics::POST_TYPE);
    }
}

/**
 * Check if the current archive style is set to Calendar
 *
 * @return bool
 */
function comic_archive_is_calendar()
{
    $archive_style = \MangaPress\Options\Options::get_option('comicarchive_page_style', 'basic');
    return $archive_style === 'calendar';
}


/**
 * Check if the current archive style is set to Gallery
 *
 * @return bool
 */
function comic_archive_is_gallery()
{
    $archive_style = \MangaPress\Options\Options::get_option('comicarchive_page_style', 'basic');

    return $archive_style === 'gallery';
}


/**
 * Check if the current archive style is set to List
 *
 * @return bool
 */
function comic_archive_is_list()
{
    $archive_style = \MangaPress\Options\Options::get_option('comicarchive_page_style', 'basic');
    return $archive_style === 'list';
}


/**
 * Get current comic archive style
 *
 * @return string
 */
function mangapress_get_comic_archive_style()
{
    return \MangaPress\Options\Options::get_option('comicarchive_page_style', 'basic');
}


/**
 * Retrieve the previous post in The Loop. We have our reasons
 *
 * @return WP_Post|false
 * @global WP_Query $wp_query
 */
function mangapress_get_previous_post_in_loop()
{
    global $wp_query;

    if ($wp_query->current_post == -1 || $wp_query->current_post == 0) {
        return false;
    }

    return $wp_query->posts[$wp_query->current_post - 1];
}


/**
 * Get the next post in the loop. Might come in handy.
 *
 * @return WP_Post|false
 * @global WP_Query $wp_query
 */
function mangapress_get_next_post_in_loop()
{
    global $wp_query;

    if ($wp_query->current_post == ($wp_query->found_posts - 1)) {
        return false;
    }

    return $wp_query->posts[$wp_query->current_post + 1];
}

/**
 * Check if theme supports mangapress cover images
 * @return bool
 */
function mangapress_theme_supports_cover_images()
{
    $theme_supports = get_theme_support('mangapress');

    if (empty($theme_supports) || !in_array('cover-images', $theme_supports[0])) {
        return false;
    }

    return true;
}

/**
 * Check if the current post has a cover image
 * @return bool|string
 */
function mangapress_has_cover_image()
{
    global $post;

    if (!$post) {
        return false;
    }

    return (bool)get_post_meta($post->ID, 'mangapress_cover_image_id', true);
}

/**
 * Get the comic's cover image
 *
 * @param null|\WP_Post $post
 * @param string $size
 * @param array $attr
 * @return string|void
 */
function mangapress_the_comic_cover($post = null, $size = 'thumbnail', $attr = [])
{
    $post = get_post($post);
    if (!$post) {
        return '';
    }

    $cover_image_id = get_post_meta($post->ID, 'mangapress_cover_image_id', true);

    if (!$cover_image_id) {
        return '';
    } else {
        echo wp_get_attachment_image($cover_image_id, $size, false, $attr);
    }
}

/**
 * mangapress_comic_navigation()
 *
 * Displays navigation for post specified by $post_id.
 *
 * @param array $args Arguments for navigation output
 * @param bool $echo Specifies whether to echo comic navigation or return it as a string
 * @return string Returns navigation string if $echo is set to false.
 * @global object $wpdb
 *
 * @since 0.1b
 *
 */
function mangapress_comic_navigation($args = [], $echo = true)
{
    global $post;

    $mp_options = \MangaPress\Options\Options::get_options();

    $defaults = [
        'container'       => 'nav',
        'container_attr'  => [
            'id'    => 'comic-navigation',
            'class' => 'comic-nav-hlist-wrapper',
        ],
        'items_wrap'      => '<ul%1$s>%2$s</ul>',
        'items_wrap_attr' => ['class' => 'comic-nav-hlist'],
        'link_wrap'       => 'li',
        'link_before'     => '',
        'link_after'      => '',
    ];

    $parsed_args = wp_parse_args($args, $defaults);
    $r           = apply_filters('mangapress_comic_navigation_args', $parsed_args);
    $args        = (object)$r;

    $group     = boolval($mp_options['basic']['group_comics']);
    $by_parent = boolval($mp_options['basic']['group_by_parent']);
    $next_post = \MangaPress\Theme\Functions\get_adjacent_comic(false, $group, $by_parent, 'mangapress_series');
    $prev_post = \MangaPress\Theme\Functions\get_adjacent_comic(true, $group, $by_parent, 'mangapress_series');

    $last_post  = \MangaPress\Theme\Functions\get_boundary_comic(false, $group, $by_parent, 'mangapress_series');
    $first_post = \MangaPress\Theme\Functions\get_boundary_comic(true, $group, $by_parent, 'mangapress_series');

    $current_page = $post->ID; // use post ID this time.

    $next_page = !isset($next_post->ID) ? $current_page : $next_post->ID;
    $prev_page = !isset($prev_post->ID) ? $current_page : $prev_post->ID;
    $last      = !isset($last_post->ID) ? $current_page : $last_post->ID;
    $first     = !isset($first_post->ID) ? $current_page : $first_post->ID;

    $first_url = get_permalink($first);
    $last_url  = get_permalink($last);
    $next_url  = get_permalink($next_page);
    $prev_url  = get_permalink($prev_page);

    $show_container = false;
    $comic_nav      = "";
    if ($args->container) {
        $show_container = true;
        $attr           = "";
        if (!empty($args->container_attr)) {
            $attr_arr = [];
            foreach ($args->container_attr as $name => $value) {
                $attr_arr[] = "{$name}=\"" . esc_attr($value) . "\"";
            }

            $attr = " " . implode(" ", $attr_arr);
        }

        $comic_nav .= "<{$args->container}$attr>";
    }

    $items_wrap_attr = "";
    if (!empty($args->items_wrap_attr)) {
        $items_attr_arr = [];
        foreach ($args->items_wrap_attr as $name => $value) {
            $items_attr_arr[] = "{$name}=\"" . esc_attr($value) . "\"";
        }

        $items_wrap_attr = " " . implode(" ", $items_attr_arr);
    }

    $items = [];

    // Here, we start processing the urls.
    // Let's do first page first.
    $first_html = "<{$args->link_wrap} class=\"link-first\">" . (($first == $current_page)
            ? '<span class="comic-nav--nolink">' . __('First', MP_DOMAIN) . '</span>'
            : '<a class="comic-link-item" href="' . $first_url . '">' . __('First', MP_DOMAIN) . '</a>')
                  . "</{$args->link_wrap}>";

    $last_html = "<{$args->link_wrap} class=\"link-last\">" .
                 (($last == $current_page)
                     ? '<span class="comic-nav--nolink">' . __('Last', MP_DOMAIN) . '</span>'
                     : '<a class="comic-link-item" href="' . $last_url . '">' . __('Last', MP_DOMAIN) . '</a>')
                 . "</{$args->link_wrap}>";

    $next_html = "<{$args->link_wrap} class=\"link-next\">" . (($next_page == $current_page)
            ? '<span class="comic-nav--nolink">' . __('Next', MP_DOMAIN) . '</span>'
            : '<a class="comic-link-item" href="' . $next_url . '">' . __('Next', MP_DOMAIN) . '</a>')
                 . "</{$args->link_wrap}>";

    $prev_html = "<{$args->link_wrap} class=\"link-prev\">" . (($prev_page == $current_page)
            ? '<span class="comic-nav--nolink">' . __('Prev', MP_DOMAIN) . '</span>'
            : '<a class="comic-link-item" href="' . $prev_url . '">' . __('Prev', MP_DOMAIN) . '</a>')
                 . "</{$args->link_wrap}>";

    $items['first'] = apply_filters('mangapress_comic_navigation_first', $first_html, $args);
    $items['prev']  = apply_filters('mangapress_comic_navigation_prev', $prev_html, $args);
    $items['next']  = apply_filters('mangapress_comic_navigation_next', $next_html, $args);
    $items['last']  = apply_filters('mangapress_comic_navigation_last', $last_html, $args);

    $items_str = implode(" ", apply_filters('mangapress_comic_navigation_items', $items, $args));

    $comic_nav .= sprintf($args->items_wrap, $items_wrap_attr, $items_str);

    if ($show_container) {
        $comic_nav .= "</{$args->container}>";
    }

    if ($echo) {
        echo $comic_nav;
    } else {
        return $comic_nav;
    }
}

// phpcs:disable
/**
 * CPT-neutral Clone of WordPress' get_calendar
 *
 * @param int $month Month number (1 through 12)
 * @param int $yr Calendar year
 * @param bool $nav Output navigation
 * @param bool $skip_empty_months Skip over months that don't contain posts
 * @param bool $abbr Optional, default is true. Use initial calendar names.
 * @param bool|array $posts
 * @param bool $echo Optional, default is true. Set to false for return.
 * @return void|string
 */
function mangapress_get_calendar($month = 0, $yr = 0, $nav = true, $skip_empty_months = false, $abbr = true, $echo = true)
{
    global $wpdb, $m, $wp_locale;

    if (!$month) {
        global $monthnum;
    } else {
        $monthnum = $month;
    }

    if (!$yr) {
        global $year;
    } else {
        $year = $yr;
    }

    $cache_key = 'mangapress_calendar-' . $year . $monthnum;

    $posts = wp_cache_get($cache_key, MP_DOMAIN);
    if (!$posts) {
        // check the month for posts—bail if none exist
        $sql   = $wpdb->prepare(
            "SELECT DISTINCT post_date, ID FROM {$wpdb->posts} WHERE "
            . "MONTH(post_date) = %s AND YEAR(post_date) = %s "
            . "AND post_type=%s "
            . "AND post_status='publish' "
            . "GROUP BY DAYOFMONTH(post_date) "
            . "ORDER BY post_date DESC",
            $monthnum,
            $year,
            \MangaPress\Posts\Comics::POST_TYPE
        );
        $posts = $wpdb->get_results($sql, OBJECT_K);

        // cache in transients for 30 days, or when a new comic post is added
        // additionally, we'll cache the markup once it's generated
        wp_cache_set($cache_key, $posts, MP_DOMAIN);
    }

    if (empty($posts)) {
        return '';
    }

    add_filter('day_link', 'mangapress_day_link', 10, 4);
    add_filter('month_link', 'mangapress_month_link', 10, 3);

    $dayswithposts = [];
    foreach ($posts as $post) {
        $date                   = date('Y-m-d', strtotime($post->post_date));
        $dayswithposts[$date][] = $post;
    }

    $unix_month = mktime(0, 0, 0, $monthnum, 1, $year);
    $last_day   = date('t', $unix_month);

    $calendar_caption = _x('%1$s %2$s', 'calendar caption');
    $calendar_output  = '<table id="mangapress-calendar-' . intval(date('m', $unix_month)) . '">
	<caption>' . sprintf(
            $calendar_caption,
            $wp_locale->get_month($monthnum),
            date('Y', $unix_month)
        ) . "</caption>
	<thead>\n
	<tr>\n";

    for ($wd_count = 0; $wd_count <= 6; $wd_count++) {
        $day_name = $abbr
            ? $wp_locale->get_weekday(($wd_count) % 7)
            : $wp_locale->get_weekday_abbrev(($wd_count) % 7);

        $wd = esc_attr($day_name);

        $calendar_output .= sprintf(
            '<th scope="col" title="%s">%s</th>' . "\n",
            $wd,
            $day_name
        );
    }

    $calendar_output .= "</tr>\n</thead>\n";
    $calendar_output .= "<tfoot>\n<tr>\n";
    $calendar_output .= "<td colspan='7'></td>";
    $calendar_output .= "</tr>\n</tfoot>\n";

    $calendar_body = "<tbody>%s</tbody>\n</table>";
    $week_days     = '';
    $week_count    = 1; // can't be higher than 5?

    for ($day = 1; $day <= $last_day; $day++) {
        $date  = mktime(0, 0, 0, $monthnum, $day, $year);
        $today = mktime(0, 0, 0);

        $day_of_week = (int)date('w', $date);
        $pad         = calendar_week_mod(date('w', $date));

        $is_today = $date === $today;

        if (isset($dayswithposts[date('Y-m-d', $date)])) {
            $week_day = sprintf(
                '<td%s><a href="%s">%s</a></td>',
                $is_today ? ' id="today"' : '',
                get_day_link($year, $monthnum, $day),
                $day
            );
        } else {
            $week_day = sprintf(
                '<td%s>%s</td>',
                $is_today ? ' id="today"' : '',
                $day
            );
        }


        if ($pad != 0 && $week_count == 1) {
            if ($day === 1) {
                $week_days .= "\t" . sprintf('<tr><td colspan="%s"></td>', $pad);
            }
            $week_days .= $week_day;
        } else {
            if ($day_of_week == 0) {
                $week_days .= '<tr>';
            }
            $week_days .= $week_day;
        }

        if ($day == $last_day) {
            $pad       = 6 - $day_of_week;
            $week_days .= "\n\t\t" . '<td class="pad" colspan="' . esc_attr($pad) . '">&nbsp;</td>';
        }


        if (6 == (int)calendar_week_mod(date('w', mktime(0, 0, 0, $monthnum, $day, $year)))) {
            $week_days .= '</tr>';
            $week_count++;
        }

    }

    $calendar_output .= sprintf($calendar_body, $week_days);

    echo $calendar_output;

    remove_filter('day_link', 'mangapress_day_link');
    remove_filter('month_link', 'mangapress_month_link');
}

/**
 * Create a date-archive permalink for Comics (for monthly links)
 *
 * @param string $monthlink Existing link to be modified or replaced
 * @param string $year
 * @param string $month
 * @return string|void
 */
function mangapress_month_link($monthlink, $year = '', $month = '')
{
    $comic           = new \MangaPress\Posts\Comics();
    $slug            = $comic->get_front_slug();
    $month_permalink = home_url("/{$slug}/{$year}/{$month}");
    return $month_permalink;
}

/**
 * Create a date-archive permalink for Comics
 *
 * @param string $daylink Existing link to be modified or replaced
 * @param string $year Year
 * @param string $month Month
 * @param string $day Day
 *
 * @return string
 */
function mangapress_day_link($daylink, $year = '', $month = '', $day = '')
{
    $comic         = new \MangaPress\Posts\Comics();
    $slug          = $comic->get_front_slug();
    $relative      = "/{$slug}/{$year}/{$month}/{$day}";
    $day_permalink = home_url($relative);
    return $day_permalink;
}


/**
 * Purge Manga+Press' calendar cache. Based on delete_get_calendar_cache()
 *
 * @see mangapress_get_calendar
 * @since 2.9
 */
function mangapress_delete_get_calendar_cache()
{
    wp_cache_delete('mangapress_get_calendar', 'mangapress_calendar');
}

add_action('save_post_mangapress_comic', 'mangapress_delete_get_calendar_cache');
add_action('delete_post', 'mangapress_delete_get_calendar_cache');
add_action('update_option_start_of_week', 'mangapress_delete_get_calendar_cache');
add_action('update_option_gmt_offset', 'mangapress_delete_get_calendar_cache');
// phpcs:enable

