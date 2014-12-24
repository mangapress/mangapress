<?php if (have_posts()) : $c = 0;?>
<ul class="mangapress-archive-list">

    <?php while (have_posts()) : the_post(); $c++; ?>
    <li class="archive-item archive-item-<?php echo $c; ?>"><a href="<?php the_permalink(); ?>">
        <?php the_title(); ?></a>
        <span class="comic-post-date"><?php the_time(MangaPress_Posts::COMIC_ARCHIVE_DATEFORMAT); ?></span>
        </li>
    <?php endwhile; ?>

</ul>
<?php endif; ?>