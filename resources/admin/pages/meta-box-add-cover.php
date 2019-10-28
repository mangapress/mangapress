<?php
/**
 * MangaPress
 *
 * @package meta-box-add-cover
 * @author  Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */
global $post;

$cover_image_id = get_post_meta($post->ID, 'mangapress_cover_image_id', true);
?>
<div id="js-image-frame--<?php echo \MangaPress\Posts\Actions::FIELD_COVER; ?>"
     class="js-image-frame hide-if-no-js"
     data-field="<?php echo \MangaPress\Posts\Actions::FIELD_COVER; ?>">
    <?php
    if (!$cover_image_id) {
        echo $this->get_remove_image_html(true);
    } else {
        echo $this->get_image_html($cover_image_id, true);
    }
    ?>
</div>
<?php wp_nonce_field(\MangaPress\Posts\Actions::NONCE_INSERT_COVER, '_insert_cover-image'); ?>
<input
        type="hidden"
        id="js-input-<?php echo \MangaPress\Posts\Actions::FIELD_COVER; ?>" class="js-mangapress-image-field"
        name="_mangapress_cover_image"
        value="<?php echo esc_attr($cover_image_id); ?>"/>

