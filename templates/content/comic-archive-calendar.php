<?php
/**
 * Simple Archive Calendar template
 * @package Manga_Press
 */
 ?>

<div id="mangapress-comic-archive-calendar">

    <?php for ($i = 1 ; $i <= 12 ; $i++) :
        $calendar = mangapress_get_calendar($i, date('Y'), false, true, true, false);  ?>

        <?php if ($calendar) : ?>
            <div class="calendar">
                <?php echo $calendar; ?>
            </div>
        <?php endif; ?>

    <?php endfor; ?>

</div>