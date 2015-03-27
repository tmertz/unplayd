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
		} else {
			$title = __( 'Enter the desired widget title', 'unplayd_plugin' );
		}
		if ( isset( $instance[ 'gamePostID' ] ) ) {
			$gameID = $instance[ 'gamePostID' ];
		}
		if ( isset( $instance[ 'gameCoverSize' ] ) ) {
			$gameCoverSize = $instance[ 'gameCoverSize' ];
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'unplayd_plugin' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'gamePostID' ); ?>"><?php _e( 'Which Game Are You Playing?', 'unplayd_plugin' ); ?></label> 
			<?php 
			$unplayd_args = array(
				'posts_per_page' => -1,
				'post_type' => 'unplayd_plugin_games',
				'order'    => 'ASC',
				'orderby'  => 'title'
			);
			
			$unplayd = new WP_Query($unplayd_args);
			if( $unplayd->have_posts() ) {
			
				$output = '<select class="widefat" name="' . $this->get_field_name( 'gamePostID' ) . '" id="unplayd-select">';
				while( $unplayd->have_posts() ) { $unplayd->the_post();
					if( get_the_id() == $instance[ 'gamePostID' ] ) {
						$output .= '<option value="'.get_the_id().'" selected="selected">';
					} else {
						$output .= '<option value="'.get_the_id().'">';
					}
					$output .= get_the_title();
					$output .= '</option>';
				}
				$output .= '</select>';
			} else {
				$output = '<p>You haven\'t added any games to your collection yet.</p>';
			}
			echo $output;
			?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'gameCoverSize' ); ?>"><?php _e( 'Game Cover Size', 'unplayd_plugin' ); ?></label> 
			<?php $image_sizes = get_image_sizes(); ?>
			<select class="widefat" name="image_size">
			<?php foreach ($image_sizes as $size_name => $size_attrs): ?>
				<option value="<?php echo $size_name ?>"><?php echo ucfirst( $size_name ); ?> (<?php echo $size_attrs['height']; ?> x <?php echo $size_attrs['width']; ?>)</option>
			<?php endforeach; ?>
			</select>
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