<?php
/**
 * Manga+Press Calendar Widget class
 *
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */

/**
 * Manga+Press-specific clone of WP_Widget_Calendar
 *
 * @package MangaPress_Widget_Calendar
 * @version $Id$
 */
class MangaPress_Widget_Calendar extends WP_Widget {


	/**
	 * PHP5 Constructor method
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'mangapress_widget_calendar',
			'description' => __( 'A calendar of your site&#8217;s archived Comic Posts.', 'mangapress' ),
		);

		parent::__construct( 'mangapress_calendar', __( 'Manga+Press Calendar', 'mangapress' ), $widget_ops );
	}


	/**
	 * Output widget markup.
	 *
	 * @param array $args Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$allowed_html = wp_kses_allowed_html( 'post' );
		echo wp_kses( $args['before_widget'], $allowed_html );
		if ( $title ) {
			echo wp_kses( $args['before_title'] . $title . $args['after_title'], $allowed_html );
		}
		echo '<div id="calendar_wrap">';
		mangapress_get_calendar();
		echo '</div>';
		echo wp_kses( $args['after_widget'], $allowed_html );
	}

	/**
	 * Update widget options.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                             WP_Widget::form().
	 *
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = wp_strip_all_tags( $new_instance['title'] );

		return $instance;
	}


	/**
	 * Outputs the settings update form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = wp_strip_all_tags( $instance['title'] );
		?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'mangapress' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
		<?php
	}
}
