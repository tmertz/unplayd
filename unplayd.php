<?php
/**
 * Plugin Name: EC Unplay'd
 * Plugin URI: http://evilcorporation.dk/projects/ec-unplayd/
 * Description: Giving Shaun Inmans 'Unplayed' the WordPress treatment. Track your gaming progress easily.
 * Version: 1.0
 * Author: Thomas Mertz
 * Author URI: http://evilcorporation.dk/
 */

function ec_unplayd_init() {
	$labels = array(
		'name'               => __( 'Games', 'ec_unplayd' ),
		'singular_name'      => __( 'Game', 'ec_unplayd' ),
		'menu_name'          => __( 'Games', 'ec_unplayd' ),
		'name_admin_bar'     => __( 'Game', 'ec_unplayd' ),
		'add_new'            => __( 'Add New', 'ec_unplayd' ),
		'add_new_item'       => __( 'Add New Game', 'ec_unplayd' ),
		'new_item'           => __( 'New Game', 'ec_unplayd' ),
		'edit_item'          => __( 'Edit Game', 'ec_unplayd' ),
		'view_item'          => __( 'View Game', 'ec_unplayd' ),
		'all_items'          => __( 'All Games', 'ec_unplayd' ),
		'search_items'       => __( 'Search Games', 'ec_unplayd' ),
		'parent_item_colon'  => __( 'Parent Games:', 'ec_unplayd' ),
		'not_found'          => __( 'No games found.', 'ec_unplayd' ),
		'not_found_in_trash' => __( 'No games found in Trash.', 'ec_unplayd' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'menu_position' 	 => 3, 
		'menu_icon' 		 => 'dashicons-welcome-view-site',
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'games' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' )
	);
	register_post_type( 'ec_unplayd_games', $args );
	
	register_taxonomy(
		'ec_unplayd_progress',
		'ec_unplayd_games',
		array(
			'label' => __( 'Progress' ),
			'rewrite' => array( 'slug' => 'progress' ),
			'hierarchical' => true,
		)
	);
    
    $ec_unplayd_progress_presets_installed = get_option( 'ec_unplayd_progress_presets_installed' );
	if( !$ec_unplayd_progress_presets_installed ) {
		$progressStates = array( 'Unplayed', 'Unbeaten', 'Beaten', 'Abandoned' );
        foreach( $progressStates as $progressState ){
            if( !term_exists( $progressState, 'ec_unplayd_progress' ) ){
                wp_insert_term( $progressState, 'ec_unplayd_progress' );
            }
        }
		update_option( 'ec_unplayd_progress_presets_installed', TRUE );
	}
	
	register_taxonomy(
		'ec_unplayd_platform',
		'ec_unplayd_games',
		array(
			'label' => __( 'Platform' ),
			'rewrite' => array( 'slug' => 'platform' ),
			'hierarchical' => true,
		)
	);
	
	$ec_unplayd_platforms_presets_installed = get_option( 'ec_unplayd_platforms_presets_installed' );
	if( !$ec_unplayd_platforms_presets_installed ) {
		$platforms = array( 
			"Xbox 360",
			"Xbox One", 
			"Xbox", 
			"Mac",
			"PC",
			"iOS", 
			"Android",
			"Wii",
			"Wii U",
			"NES",
			"SNES",
			"N64",
			"Gamecube",
			"PlayStation",
			"PlayStation 2",
			"PlayStation 3",
			"PlayStation 4",
			"Dreamcast",
			"Ouya",
			"Game Boy",
			"PlayStation Portable",
			"Nintendo 3DS",
			"PlayStation Vita",
			"N-Gage",
			"Game Boy Advance",
			"Sega Master System",
			"Nintendo DS" 
		);
        foreach( $platforms as $singlePlatform ){
            if( !term_exists( $singlePlatform, 'ec_unplayd_platform' ) ){
                wp_insert_term( $singlePlatform, 'ec_unplayd_platform' );
            }
        }
		update_option( 'ec_unplayd_platforms_presets_installed', TRUE );
	}
	
}
add_action( 'init', 'ec_unplayd_init' );

function ec_unplayd_queue() {
	wp_enqueue_style( 'unplayd-fontawesome', '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css' );
	wp_enqueue_style( 'unplayd-core', plugin_dir_url( __FILE__ ) . 'assets/css/ec-unplayd.css' );
	wp_enqueue_script( 'unplayd-js', plugin_dir_url( __FILE__ ) . 'assets/js/ec-unplayd-min.js', array('jquery'), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'ec_unplayd_queue' );

/*---------------
 *
 * LOCALIZATION
 *
 *--------------*/
function ec_unplayd_load_translations() {
	load_plugin_textdomain('ec_unplayd', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action('init', 'ec_unplayd_load_translations');

/*---------------
 *
 * SHORTCODE
 *
 *--------------*/
function show_unplayd( $atts ){
	
	$attributes = shortcode_atts( array(
		'progress' => FALSE,
		'platform' => FALSE
	), $atts );
	
	$tax_query = array(
		'relation' => 'AND'
	);
	
	if( strlen( $attributes["progress"] )>0 ) {
		array_push($tax_query, array(
			'taxonomy' => 'ec_unplayd_progress',
			'field' => 'slug',
			'terms' => $attributes["progress"]
			)
		);
	}
	if( strlen( $attributes["platform"] )>0 ) {
		array_push($tax_query, array(
			'taxonomy' => 'ec_unplayd_platform',
			'field' => 'slug',
			'terms' => $attributes["platform"]
			)
		);
	}
	
	$unplayd_args = array(
		'posts_per_page' => 10,
		'post_type' => 'ec_unplayd_games',
		'orderby' => 'title',
		'order' => 'ASC',
		'tax_query' => $tax_query
	);
	
	$unplayd = new WP_Query($unplayd_args);
	
	if( $unplayd->have_posts() ) {
		
		$output .= '<ul class="unplayd-content">';
		
		while( $unplayd->have_posts() ) { $unplayd->the_post();
		
			$excerpt = apply_filters( 'the_content', get_the_excerpt() );
			$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
			
			$terms = wp_get_post_terms( get_the_ID(), array('progress','platform') );
			$progressName = $terms[0]->name;
			$platformName = $terms[1]->name;
			
			$ratingStars = get_post_meta( get_the_ID(), 'ec_unplayd_game_rating', true);
			$i = 0;
			while( $i < $ratingStars ) {
				$ratingStarsString .= '<i class="fa fa-star"></i>';
				$i++;
			}
			
			$blankStars = 5 - $ratingStars;
			$i = 0;
			while( $i < $blankStars ) {
				$blankStarsString .= '<i class="fa fa-star-o"></i>';
				$i++;
			}
			
			$output .= '<li class="'.implode( " ", get_post_class( 'ec-unplayd unplayd' ) ).'">';
			$output .= '<h4><span class="unplayd-ratings">'.$ratingStarsString.$blankStarsString.'</span><a href="#" class="open">' . get_the_title() . '</a></h4>';
			$output .= '<div class="unplayd-body">';
			$output .= $excerpt;
			$output .= '<p class="unplayd-meta"><small>'.$platformName.' · '.$progressName.' · ' . human_time_diff( get_the_time('U'), current_time('timestamp') ).' '.__('ago', 'ec_unplayd').'</small></p>';
			$output .= '</div>';
			$output .= '</li>';
			
			unset($ratingStarsString);
			unset($blankStarsString);
		}
		$output .= '</ul>';
	
	} else {
	
		$output .= '<p>No games have been added to Unplay\'d yet.</p>';
	
	}
	
	return $output;
}
add_shortcode( 'unplayd', 'show_unplayd' );

/*---------------
 *
 * META BOXES
 *
 *--------------*/
add_action( 'add_meta_boxes', 'ec_unplayd_games_rating_metabox' );
function ec_unplayd_games_rating_metabox() {
	add_meta_box( 'unplayd-games-rating', 'Rating', 'ec_unplayd_rating_metabox', 'ec_unplayd_games', 'advanced', 'high' );
}
function ec_unplayd_rating_metabox() { 
	global $post;
	$values = get_post_custom( $post->ID );
	$selected = isset( $values['ec_unplayd_game_rating'] ) ? esc_attr( $values['ec_unplayd_game_rating'][0] ) : ”;
	wp_nonce_field( 'ec_unplayd_games_rating_nonce', 'rating_meta_box_nonce' );
?>
	<select name="ec_unplayd_game_rating">
		<option value="0" <?php selected( $selected, '0' ); ?>>0 stars</option>
		<option value="1" <?php selected( $selected, '1' ); ?>>1 stars</option>
		<option value="2" <?php selected( $selected, '2' ); ?>>2 stars</option>
		<option value="3" <?php selected( $selected, '3' ); ?>>3 stars</option>
		<option value="4" <?php selected( $selected, '4' ); ?>>4 stars</option>
		<option value="5" <?php selected( $selected, '5' ); ?>>5 stars</option>
	</select>
<?php }

add_action( 'save_post', 'ec_unplayd_rating_meta_box_save' );
function ec_unplayd_rating_meta_box_save( $post_id ) {
	
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	if( !isset( $_POST['rating_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['rating_meta_box_nonce'], 'ec_unplayd_games_rating_nonce' ) ) return;
	
	if( !current_user_can( 'edit_post' ) ) return;
	
	if( isset( $_POST['ec_unplayd_game_rating'] ) ) {
		update_post_meta( $post_id, 'ec_unplayd_game_rating', esc_attr( $_POST['ec_unplayd_game_rating'] ) );
	}
	
}