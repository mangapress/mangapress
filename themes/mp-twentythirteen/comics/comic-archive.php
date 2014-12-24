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
<div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header><!-- .entry-header -->

            <div class="entry-content">
                <?php the_content(); ?>
                <ul>
                    <?php $archives = new WP_Query(array('post_type' => 'mangapress_comic', 'post_status' => 'publish', 'posts_per_page' => -1));?>
                    <?php if ($archives->have_posts()) : while ($archives->have_posts()) : $archives->the_post(); ?>
                    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                    <?php endwhile; endif; ?>
                </ul>
            </div>
            <footer class="entry-meta">
                <?php edit_post_link(__('Edit', 'twentythirteen'), '<span class="edit-link">', '</span>'); ?>
            </footer><!-- .entry-meta -->
        </article>
    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>