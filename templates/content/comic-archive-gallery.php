<?php
/**
 * Simple Archive Gallery template
 * @package Manga_Press
 */
?>
<?php echo mangapress_archive_gallery_style(); ?>
<?php if (have_posts()) : $c = 0;?>
<ul class="mangapress-archive-gallery">

    <?php while (have_posts()) : the_post(); $c++; ?>
    <li class="archive-item archive-item-<?php echo $c; ?>">
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail(array(100, 100)); ?>
        </a>
        <p class="comic-title-caption"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
        <p class="comic-post-date"><?php the_time(MangaPress_Posts::COMIC_ARCHIVE_DATEFORMAT); ?></p>
    </li>
    <?php endwhile; ?>

</ul>
<?php endif; ?>