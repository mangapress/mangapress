<?php
/**
 * MangaPress
 *
 * @package meta-box-add-comic
 * @author Jess Green <jgreen at psy-dreamer.com>
 * @version $Id$
 * @license GPL
 */

$mangapress_image_id = get_post_thumbnail_id();
?>
<div id="js-image-frame" class="hide-if-no-js">
	<?php
	if ( ! $mangapress_image_id ) {
		echo esc_html( $this->get_remove_image_html() );
	} else {
		echo esc_html( $this->get_image_html( $mangapress_image_id ) );
	}
	?>
</div>
<?php wp_nonce_field( self::NONCE_INSERT_COMIC, '_insert_comic' ); ?>
<input type="hidden" id="js-mangapress-comic-image" name="_mangapress_comic_image" value="<?php echo esc_attr( $mangapress_image_id ); ?>" />
