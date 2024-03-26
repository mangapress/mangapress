<?php
/**
 * MangaPress
 *
 * @package set-image-link
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */
global $wp;
if ( empty( $image_html ) && ! ( $wp instanceof WP ) ) {
	die( 'No access allowed!' );
}
?>

<div class="hide-if-no-js">
	<?php echo $image_html; ?>
	<p>
		<a href="#" data-action="<?php echo self::ACTION_REMOVE_IMAGE; ?>" data-nonce="<?php echo self::NONCE_INSERT_COMIC; ?>" id="js-remove-comic-thumbnail"><?php esc_html_e( 'Remove Comic image', MP_DOMAIN ); ?></a>
	</p>
</div>
