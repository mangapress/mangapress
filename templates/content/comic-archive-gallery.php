<?php
/**
 * Simple Archive Gallery template
 *
 * @package Manga_Press
 */

$mangapress_allowed_html          = wp_kses_allowed_html( 'post' );
$mangapress_allowed_html['style'] = true;
?>
<?php echo wp_kses( mangapress_archive_gallery_style(), $mangapress_allowed_html ); ?>
<?php
if ( have_posts() ) :
	$mangapress_count = 0;
	?>
<ul class="mangapress-archive-gallery">

	<?php
	while ( have_posts() ) :
		the_post();
		++$mangapress_count;
		?>
	<li class="archive-item archive-item-<?php echo esc_attr( $mangapress_count ); ?>">
		<a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( array( 100, 100 ) ); ?>
		</a>
		<p class="comic-title-caption"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
		<p class="comic-post-date"><?php the_time( MangaPress_Posts::COMIC_ARCHIVE_DATEFORMAT ); ?></p>
	</li>
	<?php endwhile; ?>

</ul>
<?php endif; ?>
