<?php
/**
 * Latest Comic Shortcode template
 *
 * @package MangaPress\Templates\Content\Latest_Shortcode
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */
?>
    <header class="mangapress_comic_title">
        <h2><?php the_title(); ?></h2>
    </header>
    <div class="mangapress_comic-thumbnail">
        <?php the_post_thumbnail(); ?>
    </div>
<?php mangapress_comic_navigation();