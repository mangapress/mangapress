<?php if (have_posts()) : $c = 0;?>
<ul class="mangapress-archive-list">

    <?php while (have_posts()) : the_post(); $c++; ?>
    <li class="archive-item archive-item-<?php echo $c; ?>"><?php the_time(MangaPress_Posts::COMIC_ARCHIVE_DATEFORMAT); ?> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
    <?php endwhile; ?>

</ul>
<?php endif; ?>