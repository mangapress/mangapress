<?php
/**
 * Single Comic Template
 *
 * @package Manga_Press_Templates
 * @subpackage Single_Comic
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php
				// Start the Loop.
				while ( have_posts() ) : the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                    <?php twentyfourteen_post_thumbnail(); ?>

                                    <header class="entry-header">
                                            <?php mangapress_comic_navigation(); ?>
                                            <?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && twentyfourteen_categorized_blog() ) : ?>
                                            <div class="entry-meta">
                                                    <span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfourteen' ) ); ?></span>
                                            </div>
                                            <?php
                                                    endif;

                                                    if ( is_single() ) :
                                                            the_title( '<h1 class="entry-title">', '</h1>' );
                                                    else :
                                                            the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
                                                    endif;
                                            ?>

                                            <div class="entry-meta">
                                                    <?php
                                                            if ( 'post' == get_post_type() )
                                                                    twentyfourteen_posted_on();

                                                            if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
                                                    ?>
                                                    <span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyfourteen' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></span>
                                                    <?php
                                                            endif;

                                                            edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' );
                                                    ?>
                                            </div><!-- .entry-meta -->
                                    </header><!-- .entry-header -->

                                    <?php if ( is_search() ) : ?>
                                    <div class="entry-summary">
                                            <?php the_excerpt(); ?>
                                    </div><!-- .entry-summary -->
                                    <?php else : ?>
                                    <div class="entry-content">
                                            <?php
                                                    the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ) );
                                                    wp_link_pages( array(
                                                            'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
                                                            'after'       => '</div>',
                                                            'link_before' => '<span>',
                                                            'link_after'  => '</span>',
                                                    ) );
                                            ?>
                                    </div><!-- .entry-content -->
                                    <?php endif; ?>

                                    <?php the_tags( '<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>' ); ?>
                            </article><!-- #post-## -->
                            <?php
					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				endwhile;
			?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php
get_sidebar( 'content' );
get_sidebar();
get_footer();
