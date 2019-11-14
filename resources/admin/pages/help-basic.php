<?php
/**
 * Basic Options Contextual Help file
 *
 * @package help-basic
 * @author  Jess Green <jgreen AT psy-dreamer.com>
 */
?>
<h3><?php _e('Basic Options Help', MP_DOMAIN); ?></h3>
<p>
    <?php _e('This section handles the basic setup for your webcomic.', MP_DOMAIN) ?>
</p>

<dl>
    <dt><strong><?php _e('Group by Categories', MP_DOMAIN) ?></strong></dt>
    <dd>
        <?php _e('This option allows you to group your comics according to a category created under <strong>Series</strong>. When this option is used with the option below &mdash; <em>Group by Parent</em> &mdash; comics are grouped according to the parent category.', MP_DOMAIN); ?>
    </dd>
    <dt><strong><?php _e('Use Parent Category', MP_DOMAIN); ?></strong></dt>
    <dd>
        <?php _e('This options overrides the option above and groups comics according to the parent category.', MP_DOMAIN); ?>
    </dd>
    <dt><strong><?php _e('Comic Archive Page Style', MP_DOMAIN); ?></strong></dt>
    <dd>
        <?php _e('Choose an archive page style from the drop-down.', MP_DOMAIN); ?>
    </dd>
    <dt><strong><?php _e('Latest Comic Page and Comic Archive Page', MP_DOMAIN); ?></strong></dt>
    <dd>
        <p>
            <?php _e('Templates for Latest Comic Page and Comic Archive Page are (respectively) <code>page-latest-comic.php</code> and <code>archive-comic.php</code>.'); ?>
            <?php _e('These templates can be overridden by placing them in your theme directory.'); ?>
        </p>
    </dd>
</dl>