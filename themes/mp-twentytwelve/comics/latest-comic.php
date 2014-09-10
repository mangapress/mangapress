<?php
/**
 * MangaPress Default Theme
 *
 * @package Manga_Press
 * @subpackage MPDefault\LatestComic
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
get_header();
?>
<div id="primary" class="site-content">
    <div id="content" role="main">
        <?php mangapress_start_latest_comic(); ?>
        <?php if (have_posts()) :
            while (have_posts()) : the_post(); ?>


                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>

                    <?php mangapress_comic_navigation(); ?>

                    <div class="entry-content">
                        <p>
                        <?php the_post_thumbnail(get_the_ID()); ?>
                        </p>
                        <?php the_content(); ?>
                        <?php wp_link_pages(array('before' => '<div class="page-links">' . __('Pages:', 'twentytwelve'), 'after' => '</div>')); ?>
                    </div><!-- .entry-content -->
                    <footer class="entry-meta">
                    <?php edit_post_link(__('Edit', 'twentytwelve'), '<span class="edit-link">', '</span>'); ?>
                    </footer><!-- .entry-meta -->
                </article><!-- #post -->


                <?php comments_template('', true); ?>

            <?php endwhile; // end of the loop. ?>
            <?php mangapress_end_latest_comic();?>
        <?php
        else :
            locate_template(array('parts/404.php'), true);
        endif;
        ?>
    </div><!-- #content -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>