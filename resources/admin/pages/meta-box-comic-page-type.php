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
 * @global \WP_Post $posts
 */
global $wpdb, $post;

$posts        = \MangaPress\Posts\ComicPages::get_available_page_types();
$current_type = \MangaPress\Posts\ComicPages::get_page_type(get_post_field('ID', $post));
?>
<div>
    <h3><?php _e('Designated Pages', MP_DOMAIN) ?></h3>
    <?php if ($posts) {
        foreach ($posts as $type => $p) {
            $page_title = '';
            $page_id    = false;
            if ($p) {
                $page_title = get_post_field('post_title', $p);
                $page_id    = get_post_field('ID', $p);
            }
            if ($type === 'latest' || $type === 'archive') {
                echo '<p><strong>'
                     . (($type === 'latest')
                        ? __('Latest Comic Page', MP_DOMAIN) : __('Archive Comic Page', MP_DOMAIN))
                     . ':</strong> ';

                if ($page_id) {
                    echo '<a href="' . admin_url('post.php?post=' . esc_attr($page_id) . '&action=edit') . '">'
                         . esc_html($page_title) . '</a></p>';
                } else {
                    _e('Page not assigned', MP_DOMAIN);
                }
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
