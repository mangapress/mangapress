<?php
/**
 * Archive list template
 *
 * @package MangaPress\Templates\Content\Archive_List
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */
?>

<span class="comic-bookmark"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
<span class="comic-postdate"><?php the_time(\MangaPress\Posts\Comics::COMIC_ARCHIVE_DATEFORMAT); ?></span>