<?php
/**
 * MangaPress
 *
 * @package remove-image-link
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */

global $wp;
if ( ! ( $wp instanceof WP ) ) {
	die( 'No access allowed!' );
}
?>
<a href="#" id="choose-from-library-link" data-nonce="<?php echo esc_attr( wp_create_nonce( self::NONCE_INSERT_COMIC ) ); ?>" data-action="<?php echo esc_attr( self::ACTION_GET_IMAGE_HTML ); ?>"><?php esc_html_e( 'Set Comic Image', 'mangapress' ); ?></a>
