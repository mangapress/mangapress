<ul>
<?php if (have_posts()) : while (have_posts()) : the_post();
    $current_term_ID = mangapress_get_comic_term_ID();
    $prev_term_ID    = mangapress_get_comic_term_ID(mangapress_get_previous_post_in_loop());
    $next_term_ID    = mangapress_get_comic_term_ID(mangapress_get_next_post_in_loop()); ?>
    <?php if ($current_term_ID !== $prev_term_ID) : ?>
        <li>
            <h2><?php echo mangapress_get_comic_term_title(); ?></h2>
            <ul>
    <?php endif; ?>

                <li><?php the_time(MangaPress_Posts::COMIC_ARCHIVE_DATEFORMAT); ?> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>

    <?php if ($current_term_ID !== $next_term_ID) : ?>
            </ul>
        </li>        
    <?php endif; ?>
<?php endwhile; endif; ?>
</ul>
