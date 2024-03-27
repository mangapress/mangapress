<?php
/**
 * Manga+Press Options page
 *
 * @package MangaPress
 */

if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die(
		esc_html__(
			'You do not have sufficient permissions to manage options for this blog.',
			'mangapress'
		)
	);
}
$mangapress_tab = ( filter_input( INPUT_GET, 'tab' ) ?? 'basic' );
?>
<div class="wrap">
	<?php $this->options_page_tabs(); ?>

	<form action="options.php" method="post" id="mangapress_options_form">
		<?php settings_fields( 'mangapress_options' ); ?>

		<?php do_settings_sections( "mangapress_options-{$mangapress_tab}" ); ?>

		<p>
			<?php submit_button(); ?>
		</p>

	</form>
</div>
