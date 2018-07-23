<?php
/**
 * Simple Archive Gallery template
 * @package Manga_Press
 */
?>
<a href="<?php the_permalink(); ?>">
    <?php the_post_thumbnail(array(100, 100)); ?>
</a>
<p class="comic-title-caption"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
<p class="comic-post-date"><?php the_time(MangaPress_Posts::COMIC_ARCHIVE_DATEFORMAT); ?></p>