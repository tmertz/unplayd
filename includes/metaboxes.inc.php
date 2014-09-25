<?php
/*---------------
 *
 * META BOXES
 *
 *--------------*/
add_action( 'add_meta_boxes', 'unplayd_games_rating_metabox' );
function unplayd_games_rating_metabox() {
	add_meta_box( 'unplayd-games-rating', __('Rating','unplayd_plugin'), 'unplayd_rating_metabox', 'unplayd_plugin_games', 'advanced', 'high' );
}
function unplayd_rating_metabox() { 
	global $post;
	$values = get_post_custom( $post->ID );
	$selected = isset( $values['unplayd_game_rating'] ) ? esc_attr( $values['unplayd_game_rating'][0] ) : ”;
	wp_nonce_field( 'unplayd_games_rating_nonce', 'rating_meta_box_nonce' );
?>
	<select name="unplayd_game_rating">
		<option value="0" <?php selected( $selected, '0' ); ?>>0 stars</option>
		<option value="1" <?php selected( $selected, '1' ); ?>>1 stars</option>
		<option value="2" <?php selected( $selected, '2' ); ?>>2 stars</option>
		<option value="3" <?php selected( $selected, '3' ); ?>>3 stars</option>
		<option value="4" <?php selected( $selected, '4' ); ?>>4 stars</option>
		<option value="5" <?php selected( $selected, '5' ); ?>>5 stars</option>
	</select>
<?php }

add_action( 'save_post', 'unplayd_rating_meta_box_save' );
function unplayd_rating_meta_box_save( $post_id ) {
	
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	if( !isset( $_POST['rating_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['rating_meta_box_nonce'], 'unplayd_games_rating_nonce' ) ) return;
	
	if( !current_user_can( 'edit_post' ) ) return;
	
	if( isset( $_POST['unplayd_game_rating'] ) ) {
		update_post_meta( $post_id, 'unplayd_game_rating', esc_attr( $_POST['unplayd_game_rating'] ) );
	}
	
}