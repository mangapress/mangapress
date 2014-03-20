<ul>
    <?php if (have_posts()) : while (have_posts()) : the_post();
    // term switching
    ?>
        
    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
    
    <?php endwhile; endif; ?>
</ul>