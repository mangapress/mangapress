<?php
/**
 * MangaPress
 *
 * @package meta-box-add-comic
 * @author  Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */

/**
 * @global \wpdb $wpdb
 * @global \WP_Post $posts
 */
global $wpdb, $post;

$sql = "SELECT * FROM {$wpdb->postmeta} wpm "
       . "LEFT JOIN {$wpdb->posts} wp ON wp.ID = wpm.post_id "
       . "WHERE meta_value IN('latest', 'archive')";

$posts = $wpdb->get_results($sql, OBJECT_K);
?>
<div>
    <h3>Designated Pages</h3>
    <?php if ($posts) {
        foreach ($posts as $p) {
            if ($p->meta_value === 'latest' || $p->meta_value === 'archive') {
                echo '<p><strong>'
                     . (($p->meta_value === 'latest')
                        ? __('Latest Comic Page', MP_DOMAIN) : __('Archive Comic Page', MP_DOMAIN))
                     . ':</strong> ';
                echo '<a href="' . admin_url('post.php?post=' . esc_attr($p->ID) . '&action=edit') . '">'
                     . esc_html($p->post_title) . '</a></p>';
            }
        }
    } else {
        //
    } ?>
</div>
<select name="mangapress_comicpage_type">
    <option value=""> Select Page Type</option>
    <option value="latest" <?php selected('latest', get_post_meta(get_post_field('ID', $post), 'comic_page__type', true)) ?>>
        Latest Comic
        Page
    </option>
    <option value="archive" <?php selected('archive', get_post_meta(get_post_field('ID', $post), 'comic_page__type', true)) ?>>
        Comic Archive Page
    </option>
</select>
