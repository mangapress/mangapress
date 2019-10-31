<?php
/**
 * Simple Archive Calendar template
 *
 * @package Manga_Press
 */

/**
 * @global wpdb $wpdb WordPress DB object
 */
//global $wpdb;
//
//$year_results = wp_cache_get('mangapress_calendar_archive', 'calendar');
//if (!$year_results) {
//    $year_results = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) as year FROM {$wpdb->posts} WHERE post_type='mangapress_comic' GROUP BY post_date DESC");
//    wp_cache_set('mangapress_calendar_archive', $year_results, 'calendar');
//}
//
//foreach ($year_results as $year_obj) {
//    for ($i = 1; $i <= 12; $i++) {
//        mangapress_get_calendar($i, $year_obj->year, false, true);
//    }
//}
