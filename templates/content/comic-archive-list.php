<?php
/**
 * Comic Archive List template
 *
 * @package MangaPress
 */

if ( have_posts() ) :
	$mangapress_c = 0; ?>
<ul class="mangapress-archive-list">

	<?php
	while ( have_posts() ) :
		the_post();
		++$mangapress_c;
		?>
	<li class="archive-item archive-item-<?php echo esc_attr( $mangapress_c ); ?>"><a href="<?php the_permalink(); ?>">
		<?php the_title(); ?></a>
		<span class="comic-post-date"><?php the_time( MangaPress_Posts::COMIC_ARCHIVE_DATEFORMAT ); ?></span>
		</li>
	<?php endwhile; ?>

</ul>
<?php endif; ?>
