<?php
/**
 * MangaPress
 *
 * @package set-image-link
 * @author  Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */
global $wp;
if (empty($image_html) && !($wp instanceof WP)) {
    die('No access allowed!');
}

/**
 * @var boolean $is_cover
 */

$action = $is_cover ? \MangaPress\Posts\Actions::ACTION_REMOVE_COVER : \MangaPress\Posts\Actions::ACTION_REMOVE_COMIC;
$nonce  = $is_cover ? \MangaPress\Posts\Actions::NONCE_INSERT_COVER : \MangaPress\Posts\Actions::NONCE_INSERT_COMIC;
$label  = $is_cover ? esc_html(__('Remove Cover image', MP_DOMAIN)) : esc_html(__('Remove Comic image', MP_DOMAIN));
?>

<div class="hide-if-no-js">
    <?php echo $image_html; ?>
    <p>
        <a href="#" data-action="<?php echo $action; ?>"
           data-nonce="<?php echo $nonce; ?>"
           id="js-remove-comic-thumbnail"><?php echo $label; ?></a>
    </p>
</div>
