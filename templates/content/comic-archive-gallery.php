<?php
/**
 * Simple Archive Gallery template
 * @package Manga_Press
 */
?>
<style type="text/css">
    .mangapress-archive-gallery {
        font-size: 0;
    }

    .mangapress-archive-gallery > li {
        text-align: center;
        width: 125px;
        min-height: 200px;
        font-size: 12px;
        list-style: none;
        margin: 10px;
        float: left;
    }

    .mangapress-archive-gallery > li:after {
         visibility: hidden;
         display: block;
         font-size: 0;
         content: " ";
         clear: both;
         height: 0;
    }

    .comic-title-caption,
    .comic-post-date {
        text-align: center;
        margin: 0;
        padding: 0;
    }

    .comic-title-caption {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
</style>
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