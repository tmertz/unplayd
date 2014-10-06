<?php
/**
 * Adds Foo_Widget widget.
 */
class Currently_Playing_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'unplayd_currently_playing_widget', // Base ID
			__('Currently Playing', 'unplayd_plugin'), // Name
			array( 'description' => __( 'This widget output the game you have marked as currently playing.', 'unplayd_plugin' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$gameID = apply_filters( 'widget_game_id', $instance['gamePostID'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		echo '<a href="'. getThumbnailURL( $gameID, FALSE ) .'">';
		echo get_the_post_thumbnail( $gameID, 'sidebar-game-cover' );
		echo '</a>';
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Enter the desired widget title', 'unplayd_plugin' );
		}
		if ( isset( $instance[ 'gamePostID' ] ) ) {
			$gameID = $instance[ 'gamePostID' ];
		}
		else {
			$gameID = __( 'Enter the post ID for the game you\'re playing', 'unplayd_plugin' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'unplayd_plugin' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'gamePostID' ); ?>"><?php _e( 'Game ID:', 'unplayd_plugin' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'gamePostID' ); ?>" name="<?php echo $this->get_field_name( 'gamePostID' ); ?>" type="text" value="<?php echo esc_attr( $gameID ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['gamePostID'] = ( ! empty( $new_instance['gamePostID'] ) ) ? strip_tags( $new_instance['gamePostID'] ) : '';

		return $instance;
	}

} // class Foo_Widget

function register_currently_playing_widget() {
    register_widget( 'Currently_Playing_Widget' );
}
add_action( 'widgets_init', 'register_currently_playing_widget' );