<?php
/**
 * MangaPress
 *
 * @package Manga_Press
 * @subpackage Manga_Press_Templates\Single_Comic
 * @version $Id$
 * @author Jess Green <jgreen at psy-dreamer.com>
 */
?>
<?php mangapress_comic_navigation(); ?>

<div class="bookmark">
    <?php mangapress_bookmark_button(array('show_history' => true)); ?>
</div>
<div class="mangapress-media-img">
    <?php echo apply_filters('mangapress_comic_image', wp_get_attachment_image( get_post_thumbnail_id(), $thumbnail_size, false ));?>
</div>