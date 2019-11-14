<?php
/**
 * General functions used for building themes
 *
 * @package MangaPress\Helpers\Template_Functions
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
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
     * Checks if the page is the Latest Comic page
     *
     * @return bool
     * @since 4.0.0
     */
    function is_latest_comic_page()
    {
        global $wp_query;

        $queried_object = $wp_query->get_queried_object();

        if (!($queried_object instanceof \WP_Post)) {
            return false;
        }

        $page_id = (int)\MangaPress\Options\Options::get_option('latestcomic_page', 'basic');

        return ($page_id === $wp_query->get_queried_object_id());
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

        $queried_object = $wp_query->get_queried_object();

        if (!($queried_object instanceof \WP_Post)) {
            return false;
        }

        $page_id = (int)\MangaPress\Options\Options::get_option('comicarchive_page', 'basic');

        return ($page_id === $wp_query->get_queried_object_id());
    }
}

if (!function_exists('is_comic_page')) {
    /**
     * Checks if the current query is for a comic page
     * @return boolean
     * @global \WP_Query $wp_query
     */
    function is_comic_page()
    {
        global $wp_query;

        return $wp_query->get('post_type') === \MangaPress\Posts\ComicPages::POST_TYPE;
    }
}

if (!function_exists('comic_archive_is_calendar')) :
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

endif;

if (!function_exists('comic_archive_is_gallery')) :
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
endif;

if (!function_exists('comic_archive_is_list')) :
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
endif;

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
    ];

    $parsed_args = wp_parse_args($args, $defaults);

    /**
     * mangapress_comic_navigation_args
     *
     * Filter available arguments for mangapress_comic_navigation
     *
     * @param array $parsed_args {
     *  Array of arguments, already parsed by wp_parse_args
     * @type string $container Container wrapping tag, defaults to 'nav'
     * @type array $container_attr {
     *  Array of wrapper tag attributes
     * @type string $id Element's id attribute, defaults to comic-navigation
     * @type string $class Element's class attribute. Defaults to comic-nav-hlist-wrapper
     * }
     * @param string $items_wrap Inner navigation wrapping markup, defaults to <ul%1$s>%2$s</ul>
     * @param string $link_wrap Link wrapper tag, defaults to li
     * }
     * @return array
     * @since 2.7
     */
    $r    = apply_filters('mangapress_comic_navigation_args', $parsed_args);
    $args = (object)$r;

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

    /**
     * mangapress_comic_navigation_items
     *
     * Modify comic navigation items. Can be used to add links, or remove links
     *
     * @param array $items Array of navigation items
     * @param array $args Array of arguments passed from function
     * @return array
     * @since 2.7
     */
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
 * @param bool $initial Optional, default is true. Use initial calendar names.
 * @param bool $echo Optional, default is true. Set to false for return.
 * @return mixed|void
 */
function mangapress_get_calendar($month = 0, $yr = 0, $nav = true, $skip_empty_months = false, $initial = true, $echo = true)
{
    global $wpdb, $m, $wp_locale, $posts;
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
    $key = md5($m . $monthnum . $year);
    if ($cache = wp_cache_get('mangapress_get_calendar', 'calendar')) {
        if (is_array($cache) && isset($cache[$key])) {
            if ($echo) {
                /**
                 * Filter the HTML calendar output.
                 *
                 * @param string $calendar_output HTML output of the calendar.
                 * @since 2.9.0
                 *
                 */
                echo apply_filters('mangapress_get_calendar', $cache[$key]);
                return '';
            } else {
                /** This filter is documented in wp-includes/general-template.php */
                return apply_filters('mangapress_get_calendar', $cache[$key]);
            }
        }
    }
    if (!is_array($cache))
        $cache = [];
    // Quick check. If we have no posts at all, abort!
    if (!$posts) {
        $gotsome = $wpdb->get_var("SELECT 1 as test FROM $wpdb->posts WHERE post_type = '" . \MangaPress\Posts\Comics::POST_TYPE . "' AND post_status = 'publish' LIMIT 1");
        if (!$gotsome) {
            $cache[$key] = '';
            wp_cache_set('mangapress_get_calendar', $cache, 'mangapress_calendar');
            return '';
        }
    }
    if (isset($_GET['w']))
        $w = '' . intval($_GET['w']);
    // week_begins = 0 stands for Sunday
    $week_begins = intval(get_option('start_of_week'));
    // Let's figure out when we are
    if (!empty($monthnum) && !empty($year)) {
        $thismonth = '' . zeroise(intval($monthnum), 2);
        $thisyear  = '' . intval($year);
    } elseif (!empty($w)) {
        // We need to get the month from MySQL
        $thisyear  = '' . intval(substr($m, 0, 4));
        $d         = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
        $thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('{$thisyear}0101', INTERVAL $d DAY) ), '%m')");
    } elseif (!empty($m)) {
        $thisyear = '' . intval(substr($m, 0, 4));
        if (strlen($m) < 6)
            $thismonth = '01';
        else
            $thismonth = '' . zeroise(intval(substr($m, 4, 2)), 2);
    } else {
        $thisyear  = gmdate('Y', current_time('timestamp'));
        $thismonth = gmdate('m', current_time('timestamp'));
    }
    $unixmonth = mktime(0, 0, 0, $thismonth, 1, $thisyear);
    $last_day  = date('t', $unixmonth);
    $previous  = '';
    $next      = '';
    if ($nav) {
        // Get the next and previous month and year with at least one post
        $previous = $wpdb->get_row("SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
            FROM $wpdb->posts
            WHERE post_date < '$thisyear-$thismonth-01'
            AND post_type = '" . \MangaPress\Posts\Comics::POST_TYPE . "' AND post_status = 'publish'
                ORDER BY post_date DESC
                LIMIT 1");
        $next     = $wpdb->get_row("SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
            FROM $wpdb->posts
            WHERE post_date > '$thisyear-$thismonth-{$last_day} 23:59:59'
            AND post_type = 'post' AND post_status = 'publish'
                ORDER BY post_date ASC
                LIMIT 1");
    }
    /* translators: Calendar caption: 1: month name, 2: 4-digit year */
    $calendar_caption = _x('%1$s %2$s', 'calendar caption');
    $calendar_output  = '<table id="manga-press-calendar">
	<caption>' . sprintf($calendar_caption, $wp_locale->get_month($thismonth), date('Y', $unixmonth)) . '</caption>
	<thead>
	<tr>';
    $myweek           = [];
    for ($wdcount = 0; $wdcount <= 6; $wdcount++) {
        $myweek[] = $wp_locale->get_weekday(($wdcount + $week_begins) % 7);
    }
    foreach ($myweek as $wd) {
        $day_name        = (true == $initial) ? $wp_locale->get_weekday_initial($wd) : $wp_locale->get_weekday_abbrev($wd);
        $wd              = esc_attr($wd);
        $calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
    }
    add_filter('month_link', 'mangapress_month_link', 10, 3);
    $calendar_output .= '
	</tr>
	</thead>';
    if ($nav) {
        $calendar_output .= "
        <tfoot>
        <tr>";
        if ($previous) {
            $calendar_output .= "\n\t\t" . '<td colspan="3" id="prev"><a href="' . get_month_link($previous->year, $previous->month) . '">&laquo; ' . $wp_locale->get_month_abbrev($wp_locale->get_month($previous->month)) . '</a></td>';
        } else {
            $calendar_output .= "\n\t\t" . '<td colspan="3" id="prev" class="pad">&nbsp;</td>';
        }
        $calendar_output .= "\n\t\t" . '<td class="pad">&nbsp;</td>';
        if ($next) {
            $calendar_output .= "\n\t\t" . '<td colspan="3" id="next"><a href="' . get_month_link($next->year, $next->month) . '">' . $wp_locale->get_month_abbrev($wp_locale->get_month($next->month)) . ' &raquo;</a></td>';
        } else {
            $calendar_output .= "\n\t\t" . '<td colspan="3" id="next" class="pad">&nbsp;</td>';
        }
        $calendar_output .= "
        	</tr>
	    </tfoot>";
    } else {
        $calendar_output .= "<tfoot><tr><td colspan=\"7\">&nbsp;</td></tr></tfoot>";
    }
    remove_filter('month_link', 'mangapress_month_link');
    $calendar_output .= '
	<tbody>
	<tr>';
    // Get days with posts
    $dayswithposts = $wpdb->get_results("SELECT DISTINCT DAYOFMONTH(post_date)
		FROM $wpdb->posts WHERE post_date >= '{$thisyear}-{$thismonth}-01 00:00:00'
		AND post_type = '" . \MangaPress\Posts\Comics::POST_TYPE . "' AND post_status = 'publish'
		AND post_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59'", ARRAY_N);
    if ($dayswithposts) {
        foreach ((array)$dayswithposts as $daywith) {
            $daywithpost[] = $daywith[0];
        }
    } else {
        $daywithpost = [];
    }
    if (empty($daywithpost) && $skip_empty_months) {
        return;
    }
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'camino') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false)
        $ak_title_separator = "\n";
    else
        $ak_title_separator = ', ';
    $ak_titles_for_day = [];
    $ak_post_titles    = $wpdb->get_results("SELECT ID, post_title, DAYOFMONTH(post_date) as dom "
                                            . "FROM $wpdb->posts "
                                            . "WHERE post_date >= '{$thisyear}-{$thismonth}-01 00:00:00' "
                                            . "AND post_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59' "
                                            . "AND post_type = '" . \MangaPress\Posts\Comics::POST_TYPE . "' AND post_status = 'publish'"
    );
    if ($ak_post_titles) {
        foreach ((array)$ak_post_titles as $ak_post_title) {
            /** This filter is documented in wp-includes/post-template.php */
            $post_title = esc_attr(apply_filters('the_title', $ak_post_title->post_title, $ak_post_title->ID));
            if (empty($ak_titles_for_day['day_' . $ak_post_title->dom]))
                $ak_titles_for_day['day_' . $ak_post_title->dom] = '';
            if (empty($ak_titles_for_day["$ak_post_title->dom"])) // first one
                $ak_titles_for_day["$ak_post_title->dom"] = $post_title;
            else
                $ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . $post_title;
        }
    }
    // See how much we should pad in the beginning
    $pad = calendar_week_mod(date('w', $unixmonth) - $week_begins);
    if (0 != $pad)
        $calendar_output .= "\n\t\t" . '<td colspan="' . esc_attr($pad) . '" class="pad">&nbsp;</td>';
    $daysinmonth = intval(date('t', $unixmonth));
    for ($day = 1; $day <= $daysinmonth; ++$day) {
        if (isset($newrow) && $newrow)
            $calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
        $newrow = false;
        if ($day == gmdate('j', current_time('timestamp')) && $thismonth == gmdate('m', current_time('timestamp')) && $thisyear == gmdate('Y', current_time('timestamp')))
            $calendar_output .= '<td id="today">';
        else
            $calendar_output .= '<td>';
        if (in_array($day, $daywithpost)) { // any posts today?
            add_filter('day_link', 'mangapress_day_link', 10, 4);
            $calendar_output .= '<a href="' . get_day_link($thisyear, $thismonth, $day) . '" title="' . esc_attr($ak_titles_for_day[$day]) . "\">$day</a>";
            remove_filter('day_link', 'mangapress_day_link');
        } else
            $calendar_output .= $day;
        $calendar_output .= '</td>';
        if (6 == calendar_week_mod(date('w', mktime(0, 0, 0, $thismonth, $day, $thisyear)) - $week_begins))
            $newrow = true;
    }
    $pad = 7 - calendar_week_mod(date('w', mktime(0, 0, 0, $thismonth, $day, $thisyear)) - $week_begins);
    if ($pad != 0 && $pad != 7)
        $calendar_output .= "\n\t\t" . '<td class="pad" colspan="' . esc_attr($pad) . '">&nbsp;</td>';
    $calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";
    $cache[$key]     = $calendar_output;
    wp_cache_set('mangapress_get_calendar', $cache, 'mangapress_calendar');
    if ($echo) {
        /**
         * Filter the HTML calendar output.
         *
         * @param string $calendar_output HTML output of the calendar.
         * @since 2.9
         *
         */
        echo apply_filters('mangapress_get_calendar', $calendar_output);
    } else {
        return apply_filters('mangapress_get_calendar', $calendar_output);
    }
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

/**
 * Create a date-archive permalink for Comics (for monthly links)
 *
 * @param string $monthlink Existing link to be modified or replaced
 * @param string $year
 * @param string $month
 * @return string|void
 * @uses 'month_link'
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
 * @uses 'day_link'
 */
function mangapress_day_link($daylink, $year = '', $month = '', $day = '')
{
    $comic         = new \MangaPress\Posts\Comics();
    $slug          = $comic->get_front_slug();
    $relative      = "/{$slug}/{$year}/{$month}/{$day}";
    $day_permalink = home_url($relative);
    return $day_permalink;
}
// phpcs:enable

