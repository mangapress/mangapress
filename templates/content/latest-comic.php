<h2><?php the_title(); ?></h2>

<div class="media-img">
    <?php the_post_thumbnail(get_the_ID(), $thumbnail_size);?>
</div>

<?php mangapress_comic_navigation(); ?>

<div class="entry-update-content">
    <?php the_content(); ?>
</div>