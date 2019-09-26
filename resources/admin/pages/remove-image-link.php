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
?>
<a href="#" id="choose-from-library-link" data-nonce="<?php echo wp_create_nonce(MangaPress\Posts::NONCE_INSERT_COMIC) ?>" data-action="<?php echo esc_attr(MangaPress\Posts::ACTION_GET_IMAGE_HTML) ?>"><?php _e('Set Comic Image', MP_DOMAIN) ?></a>
