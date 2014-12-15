<h2 class="mangapress-comic-title"><?php the_title(); ?></h2>

<div class="mangapress-media-img">
    <?php the_post_thumbnail(get_the_ID(), $thumbnail_size);?>
</div>

<?php mangapress_comic_navigation(); ?>

<div class="mangapres-entry-content">
    <?php the_content(); ?>
</div>