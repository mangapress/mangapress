<?php $twitter_username = MangaPress\Plugin\Bootstrap::get_instance()->get_option('comic_page', 'twitter_site_handle'); ?>
    <!-- Twitter Card Block -->
    <meta name="twitter:card" content="summary_large_image">
    <?php if ($twitter_username) : ?>
    <meta name="twitter:site" content="@<?php echo esc_attr($twitter_username) ?>">
    <?php endif; ?>
    <meta name="twitter:title" content="<?php the_title() ?>">
    <meta name="twitter:description" content="<?php the_excerpt(); ?>">
    <meta name="twitter:image" content="<?php the_post_thumbnail_url('large'); ?>">
    <!-- END Twitter Card block -->

