<?php
/**
 * MangaPress Default Theme
 *
 * @package Manga_Press
 * @subpackage MPDefault\LatestComic
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
get_header(); ?>
<div id="primary">
    <div id="content" role="main">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header><!-- .entry-header -->

            <div class="entry-content">
                <ul>
                <?php $series_tax = get_terms('mangapress_series');
                if (empty($series_tax)): ?>
                    <li><?php _e('No Comics', 'mp-twentyeleven'); ?></li>
                <?php else :
                    $count = 0;
                    foreach ($series_tax as $series) :
                        $count++;
                ?>
                    <li class="series series-<?php echo $count; ?> series-<?php echo sanitize_html_class($series->slug); ?>">
                        <h3>
                            <a href="<?php echo get_category_link($series) ?>"><?php echo apply_filters('the_title', $series->name) ?></a>
                        </h3>
                        <ul>
                            <?php
                            $all_comics = new WP_Query(array(
                                'posts_per_page' => -1,
                                'tax_query'      => array(
                                    'relation' => 'AND',
                                    array(
                                        'taxonomy'   => 'mangapress_series',
                                        'field'      => 'slug',
                                        'terms'      => $series->slug,
                                    ),
                                )
                            ));
                            if ($all_comics->have_posts()) : $comic_count = 0; ?>
                            <?php while($all_comics->have_posts()) : $all_comics->the_post();
                                    $comic_count++;
                                ?>
                                <li class="comics comic-<?php echo $comic_count; ?>"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> (<?php the_time('m/d/Y'); ?>)</li>
                            <?php endwhile; endif;  ?>
                        </ul>
                    </li>
                    <?php endforeach; endif; ?>
                </ul>
            </div>
            <footer class="entry-meta">
                <?php edit_post_link(__('Edit', 'twentyeleven'), '<span class="edit-link">', '</span>'); ?>
            </footer><!-- .entry-meta -->
        </article>
    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>