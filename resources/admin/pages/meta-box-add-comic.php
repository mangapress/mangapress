<?php
/**
 * MangaPress
 *
 * @package meta-box-add-comic
 * @author  Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */
$image_ID = get_post_thumbnail_id();
?>
<div id="js-image-frame--<?php echo \MangaPress\Posts\Actions::FIELD_COMIC ?>"
     class="js-image-frame hide-if-no-js"
     data-field="<?php echo \MangaPress\Posts\Actions::FIELD_COMIC ?>">
    <?php
    if (!$image_ID) {
        echo $this->get_remove_image_html();
    } else {
        echo $this->get_image_html($image_ID);
    }
    ?>
</div>
<?php wp_nonce_field(\MangaPress\Posts\Actions::NONCE_INSERT_COMIC, '_insert_comic'); ?>
<input
        type="hidden"
        id="js-input-<?php echo \MangaPress\Posts\Actions::FIELD_COMIC ?>" class="js-mangapress-image-field"
        name="_mangapress_comic_image"
        value="<?php echo esc_attr($image_ID); ?>"/>
