<?php
/**
 * Simple Archive Gallery template
 *
 * @package MangaPress\Templates\Content\Archive_Gallery
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */
?>
<a href="<?php the_permalink(); ?>">
    <?php the_post_thumbnail([100, 100]); ?>
</a>
<p class="comic-title-caption"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
<p class="comic-post-date"><?php the_time(\MangaPress\Posts\Comics::COMIC_ARCHIVE_DATEFORMAT); ?></p>