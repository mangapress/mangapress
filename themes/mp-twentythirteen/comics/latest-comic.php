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

<div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">
        <?php mangapress_start_latest_comic(); ?>
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        <div class="entry-meta">
                                <?php twentythirteen_entry_meta(); ?>
                                <?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
                        </div><!-- .entry-meta -->
                    </header><!-- .entry-header -->

                    <nav id="comic-navigation" class="navigation post-navigation" role="navigation">
                        <h1 class="screen-reader-text"><?php _e( 'Post navigation', 'twentythirteen' ); ?></h1>
                        <div class="nav-links">
                            <?php mangapress_comic_navigation(null, array('container' => '')); ?>
                        </div>
                    </nav>

                    <div class="entry-content">
                        <p>
                        <?php the_post_thumbnail(get_the_ID(), array('class' => 'mangapress-comic-img')); ?>
                        </p>
                        <?php the_content(); ?>
                        <?php wp_link_pages(array('before' => '<div class="page-links">' . __('Pages:', 'twentytwelve'), 'after' => '</div>')); ?>
                    </div><!-- .entry-content -->
                    <footer class="entry-meta">
                    <?php if ( comments_open() && ! is_single() ) : ?>
                        <div class="comments-link">
                                <?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a comment', 'twentythirteen' ) . '</span>', __( 'One comment so far', 'twentythirteen' ), __( 'View all % comments', 'twentythirteen' ) ); ?>
                        </div><!-- .comments-link -->
                    <?php endif; // comments_open() ?>

                    <?php if ( is_single() && get_the_author_meta( 'description' ) && is_multi_author() ) : ?>
                            <?php get_template_part( 'author-bio' ); ?>
                    <?php endif; ?>
                    </footer><!-- .entry-meta -->
                </article><!-- #post -->

            <?php endwhile; // end of the loop. ?>
            <?php mangapress_end_latest_comic(); ?>
        <?php
        else :
            get_template_part('comics/error', 'comic');
        endif;
        ?>
    </div><!-- #content -->
</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>