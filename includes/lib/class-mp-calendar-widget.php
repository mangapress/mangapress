<?php
/**
 * Manga+Press Calendar Widget class
 *
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <support@manga-press.com>
 */
namespace MangaPress\Lib;
 /**
  * Manga+Press-specific clone of WP_Widget_Calendar
  *
  * @package Widget_Calendar
  * @version $Id$
  */
class Widget_Calendar extends \WP_Widget
{

    /**
     * PHP5 Constructor method
     */
    public function __construct()
    {
        $widget_ops = array(
            'classname' => 'mangapress_widget_calendar',
            'description' => __( 'A calendar of your site&#8217;s archived Comic Posts.', MP_DOMAIN)
        );

        parent::__construct('mangapress_calendar', __('Manga+Press Calendar', MP_DOMAIN), $widget_ops);
    }


    /**
     *
     */
    public function widget($args, $instance)
    {

        /** This filter is documented in wp-includes/default-widgets.php */
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $args['before_widget'];
        if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        echo '<div id="calendar_wrap">';
        mangapress_get_calendar();
        echo '</div>';
        echo $args['after_widget'];
    }


    /**
     *
     */
    public function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }


    /**
     *
     */
    public function form( $instance )
    {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
        $title = strip_tags($instance['title']);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', MP_DOMAIN); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <?php
    }
}
