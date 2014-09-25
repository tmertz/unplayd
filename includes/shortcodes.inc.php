<?php
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
			'taxonomy' => 'unplayd_plugin_progress',
			'field' => 'slug',
			'terms' => $attributes["progress"]
			)
		);
	}
	if( strlen( $attributes["platform"] )>0 ) {
		array_push($tax_query, array(
			'taxonomy' => 'unplayd_plugin_platform',
			'field' => 'slug',
			'terms' => $attributes["platform"]
			)
		);
	}
	
	$unplayd_args = array(
		'posts_per_page' => 10,
		'post_type' => 'unplayd_plugin_games',
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
			
			$ratingStars = get_post_meta( get_the_ID(), 'unplayd_plugin_game_rating', true);
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
			$output .= '<p class="unplayd-meta"><small>'.$platformName.' · '.$progressName.' · ' . human_time_diff( get_the_time('U'), current_time('timestamp') ).' '.__('ago', 'unplayd_plugin').'</small></p>';
			$output .= '</div>';
			$output .= '</li>';
			
			unset($ratingStarsString);
			unset($blankStarsString);
		}
		$output .= '</ul>';
	
	} else {
	
		$output .= '<p>'.__( 'No games have been added to Unplay\'d yet.','unplayd_plugin' ).'</p>';
	
	}
	
	return $output;
}
add_shortcode( 'unplayd', 'show_unplayd' );