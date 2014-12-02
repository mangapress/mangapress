<ul>
<?php
if (have_posts()) : while (have_posts()) : the_post(); ?>
    <li><?php the_time(MangaPress_Posts::COMIC_ARCHIVE_DATEFORMAT); ?> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
<?php endwhile; endif; ?>
</ul>