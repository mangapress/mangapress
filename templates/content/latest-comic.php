<h2 class="mangapress-comic-title">
    <?php the_title(); ?>
</h2>

<div class="mangapress-media-img">
    <?php if ($use_lightbox) : ?>
        <a href="#" id="mangapress-lightbox-trigger" data-src="<?php echo esc_url($lightbox_image) ?>" data-img-width="<?php echo intval($lightbox_image_width); ?>" data-img-height="<?php echo intval($lightbox_image_height); ?>">
    <?php endif; ?>
    <?php the_post_thumbnail(get_the_ID(), $thumbnail_size);?>
    <?php if ($use_lightbox) : ?>
        </a>
    <?php endif; ?>
</div>

<?php mangapress_comic_navigation(); ?>

<div class="mangapress-entry-content">
    <?php the_content(); ?>
</div>