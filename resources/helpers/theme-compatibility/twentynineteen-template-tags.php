<?php
/**
 * Helper functions for TwentyNineteen
 * @package MangaPress\Helpers\Theme_Compatibility\TwentyNineteen
 * @version $Id$
 * @author Jess Green <support@manga-press.com>
 */

/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 */
function mangapress_twentynineteen_comic_cover()
{
    if (!twentynineteen_can_show_post_thumbnail()) {
        return;
    }

    if (is_singular()) :
        ?>

        <figure class="post-thumbnail">
            <?php mangapress_the_comic_cover(); ?>
        </figure><!-- .post-thumbnail -->

    <?php
    else :
        ?>

        <figure class="post-thumbnail">
            <a class="post-thumbnail-inner" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php mangapress_the_comic_cover('post-thumbnail'); ?>
            </a>
        </figure>

    <?php
    endif; // End is_singular().
}