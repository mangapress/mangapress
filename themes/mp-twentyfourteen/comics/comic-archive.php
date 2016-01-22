<?php
/**
 * MangaPress Default Theme
 *
 * @package Manga_Press
 * @subpackage MPDefault\LatestComic
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
get_header();?>
<div id="main-content" class="main-content">
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php twentyfourteen_post_thumbnail(); ?>
                <?php the_title( '<header class="entry-header"><h1 class="entry-title">', '</h1></header><!-- .entry-header -->' ); ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                    <ul>
                        <?php $archives = mangapress_get_all_comics_for_archive(); ?>
                        <?php if ($archives->have_posts()) : while ($archives->have_posts()) : $archives->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                        <?php endwhile; endif; ?>
                    </ul>
                </div>
                <footer class="entry-meta">
                    <?php edit_post_link(__('Edit', 'twentyfourteen'), '<span class="edit-link">', '</span>'); ?>
                </footer><!-- .entry-meta -->
            </article>
        </div><!-- #content -->
    <?php get_sidebar( 'content' ); ?>
    </div><!-- #primary -->
</div>

<?php
get_sidebar();
get_footer();