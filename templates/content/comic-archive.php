<ul>   
<?php $series_tax = get_terms('mangapress_series');
if (empty($series_tax)): ?>
    <li>No Comics</li>
<?php else : 
    foreach ($series_tax as $series) : ?>
    
    <li>
        <h3><a href="<?php echo get_category_link($series) ?>"><?php echo $series->name ?></a></h3>
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
            if ($all_comics->have_posts()) : ?>
            <?php while($all_comics->have_posts()) : $all_comics->the_post(); ?>
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> (<?php the_time('m/d/Y'); ?>)</li>
            <?php endwhile; ?>
            <?php endif; 
                endforeach;
            endif;
            ?>
        </ul>
    </li>
</ul>