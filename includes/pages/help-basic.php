<?php
/**
 * Basic Options Contextual Help file
 *
 * @package help-basic
 * @author Jess Green <jgreen AT psy-dreamer.com>
 */
?>
<h3><?php _e( 'Basic Options Help', MP_DOMAIN ); ?></h3>
<p>
	<?php _e( 'This section handles the basic setup for your webcomic.', MP_DOMAIN ); ?>
</p>

<dl>
	<dt><strong><?php _e( 'Group by Categories', MP_DOMAIN ); ?></strong></dt>
	<dd>
		<?php _e( 'This option allows you to group your comics according to a category created under <strong>Series</strong>. When this option is used with the option below &mdash; <em>Group by Parent</em> &mdash; comics are grouped according to the parent category.', MP_DOMAIN ); ?>
	</dd>
	<dt><strong><?php _e( 'Use Parent Category', MP_DOMAIN ); ?></strong></dt>
	<dd>
		<?php _e( 'This options overrides the option above and groups comics according to the parent category.', MP_DOMAIN ); ?>
	</dd>
	<dt><strong><?php _e( 'Latest Comic Page', MP_DOMAIN ); ?></strong></dt>
	<dd>
		<?php _e( 'Select an available page from the drop-down to use as your Latest Comic page. This page displays the most recent comic posted.', MP_DOMAIN ); ?>
	</dd>
	<dt><strong><?php _e( 'Comic Archive Page', MP_DOMAIN ); ?></strong></dt>
	<dd>
		<?php _e( 'Select an available page from the drop-down to use as your Comic Archive page. This page displays all comics in chronological order, starting with the most recent.', MP_DOMAIN ); ?>
	</dd>
	<dt><strong><?php echo _e( 'Comic Archive Page Style', MP_DOMAIN ); ?></strong></dt>
	<dd>
		<?php _e( 'Select an archive page style from the drop-down. This option can be overridden by adding a comic-archive-*.php template to your theme inside the <code>comics</code> sub-directory.', MP_DOMAIN ); ?>
		<?php _e( 'Available templates are: comic-archive-list.php, comic-archive-calendar.php, and comic-archive-gallery.php', MP_DOMAIN ); ?>
	</dd>
</dl>