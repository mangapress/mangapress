<?php
/**
 * MangaPress
 * 
 * @package meta-box-add-comic
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */
$image_ID = get_post_thumbnail_id();
?>
<div id="js-image-frame" class="hide-if-no-js">
    <?php 
    if (!$image_ID) {
        echo $this->get_remove_image_html();
    } else {
        echo $this->get_image_html($image_ID);
    }
    ?>
</div>
<?php wp_nonce_field(self::NONCE_INSERT_COMIC); ?>
<input type="hidden" id="js-mangapress-comic-image" name="_mangapress_comic_image" value="<?php echo esc_attr($image_ID); ?>" />
