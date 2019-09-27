<?php


namespace MangaPress\Widgets;

/**
 * Class Calendar
 * @package MangaPress\Widgets
 */
class Calendar extends \WP_Widget
{
    /**
     * PHP5 Constructor method
     */
    public function __construct()
    {
        $widget_ops = [
            'classname'   => 'mangapress_widget_calendar',
            'description' => __('A calendar of your site&#8217;s archived Comic Posts.', MP_DOMAIN),
        ];

        parent::__construct('mangapress_calendar', __('Manga+Press Calendar', MP_DOMAIN), $widget_ops);
    }


    /**
     * @see \WP_Widget::widget()
     * @inheritDoc
     */
    public function widget($args, $instance)
    {

        /**
         * This filter is documented in wp-includes/default-widgets.php
         */
        $title = apply_filters(
            'widget_title',
            empty($instance['title']) ? '' : $instance['title'],
            $instance,
            $this->id_base
        );

        echo $args['before_widget'];
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        echo '<div id="calendar_wrap">';
        mangapress_get_calendar();
        echo '</div>';
        echo $args['after_widget'];
    }


    /**
     * @see \WP_Widget::update()
     * @inheritDoc
     */
    public function update($new_instance, $old_instance)
    {
        $instance          = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }


    /**
     * @see \WP_Widget::form()
     * @inheritDoc
     */
    public function form($instance)
    {
        $instance = wp_parse_args((array)$instance, ['title' => '']);
        $title    = strip_tags($instance['title']);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', MP_DOMAIN); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/></p>
        <?php
    }
}
