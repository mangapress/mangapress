<?php
/**
 * Basic Options Contextual Help file
 *
 * @package help-basic
 * @author Jess Green <jgreen AT psy-dreamer.com>
 */
?>
<h3><?php esc_html_e( 'Basic Options Help', 'mangapress' ); ?></h3>
<p>
	<?php esc_html_e( 'This section handles the basic setup for your webcomic.', 'mangapress' ); ?>
</p>

<dl>
	<dt><strong><?php esc_html_e( 'Group by Categories', 'mangapress' ); ?></strong></dt>
	<dd>
		<?php esc_html_e( 'This option allows you to group your comics according to a category created under <strong>Series</strong>. When this option is used with the option below &mdash; <em>Group by Parent</em> &mdash; comics are grouped according to the parent category.', 'mangapress' ); ?>
	</dd>
	<dt><strong><?php esc_html_e( 'Use Parent Category', 'mangapress' ); ?></strong></dt>
	<dd>
		<?php esc_html_e( 'This options overrides the option above and groups comics according to the parent category.', 'mangapress' ); ?>
	</dd>
	<dt><strong><?php esc_html_e( 'Latest Comic Page', 'mangapress' ); ?></strong></dt>
	<dd>
		<?php esc_html_e( 'Select an available page from the drop-down to use as your Latest Comic page. This page displays the most recent comic posted.', 'mangapress' ); ?>
	</dd>
	<dt><strong><?php esc_html_e( 'Comic Archive Page', 'mangapress' ); ?></strong></dt>
	<dd>
		<?php esc_html_e( 'Select an available page from the drop-down to use as your Comic Archive page. This page displays all comics in chronological order, starting with the most recent.', 'mangapress' ); ?>
	</dd>
	<dt><strong><?php echo esc_html_e( 'Comic Archive Page Style', 'mangapress' ); ?></strong></dt>
	<dd>
		<?php esc_html_e( 'Select an archive page style from the drop-down. This option can be overridden by adding a comic-archive-*.php template to your theme inside the <code>comics</code> sub-directory.', 'mangapress' ); ?>
		<?php esc_html_e( 'Available templates are: comic-archive-list.php, comic-archive-calendar.php, and comic-archive-gallery.php', 'mangapress' ); ?>
	</dd>
</dl>
