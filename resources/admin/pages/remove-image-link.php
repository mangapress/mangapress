<?php
/**
 * MangaPress
 *
 * @package remove-image-link
 * @author  Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */
global $wp;
if (!($wp instanceof WP)) {
    die('No access allowed!');
}

/**
 * @var boolean $is_cover
 */
$nonce  = $is_cover ? \MangaPress\Posts\Actions::NONCE_INSERT_COVER : \MangaPress\Posts\Actions::NONCE_INSERT_COMIC;
$action = $is_cover ? \MangaPress\Posts\Actions::ACTION_INSERT_COVER : \MangaPress\Posts\Actions::ACTION_INSERT_COMIC;
?>
<a href="#"
   id="choose-from-library-link"
   data-nonce="<?php echo wp_create_nonce($nonce) ?>"
   data-action="<?php echo esc_attr($action) ?>">
    <?php
    if ($is_cover) {
        _e('Set Cover Image', MP_DOMAIN);
    } else {
        _e('Set Comic Image', MP_DOMAIN);
    }
    ?>
</a>
