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

$posts        = $wpdb->get_results($sql, OBJECT_K);
$current_type = \MangaPress\Posts\ComicPages::get_page_type(get_post_field('ID', $post));
?>
<div>
    <h3><?php _e('Designated Pages', MP_DOMAIN) ?></h3>
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
        _e('No pages assigned to Latest Comic or Comic Archive page', MP_DOMAIN);
    } ?>
</div>
<select name="mangapress_comicpage_type">
    <option value=""><?php _e('Select Page Type', MP_DOMAIN); ?></option>
    <option value="latest" <?php selected('latest', $current_type) ?>>
        <?php _e('Latest Comic Page', MP_DOMAIN) ?>
    </option>
    <option value="archive" <?php selected('archive', $current_type) ?>>
        <?php _e('Comic Archive Page', MP_DOMAIN); ?>
    </option>
</select>
