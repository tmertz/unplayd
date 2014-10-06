<?php
/**
 * Plugin Name: Unplay'd
 * Plugin URI: http://evilcorporation.dk/projects/ec-unplayd/
 * Description: Giving Shaun Inmans 'Unplayed' the WordPress treatment. Track your gaming progress easily.
 * Version: 1.0
 * Author: Thomas Mertz
 * Author URI: http://ihateithe.re/
 */

/*---------------
 *
 * LOCALIZATION
 *
 *--------------*/
function unplayd_load_translations() {
	load_plugin_textdomain('unplayd_plugin', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action('init', 'unplayd_load_translations');

/*---------------
 *
 * INIT AND CPT REGISTRATION
 *
 *--------------*/
function unplayd_plugin_init() {
	$labels = array(
		'name'               => __( 'Games', 'unplayd_plugin' ),
		'singular_name'      => __( 'Game', 'unplayd_plugin' ),
		'menu_name'          => __( 'Unplay\'d', 'unplayd_plugin' ),
		'name_admin_bar'     => __( 'Game', 'unplayd_plugin' ),
		'add_new'            => __( 'Add New', 'unplayd_plugin' ),
		'add_new_item'       => __( 'Add New Game', 'unplayd_plugin' ),
		'new_item'           => __( 'New Game', 'unplayd_plugin' ),
		'edit_item'          => __( 'Edit Game', 'unplayd_plugin' ),
		'view_item'          => __( 'View Game', 'unplayd_plugin' ),
		'all_items'          => __( 'All Games', 'unplayd_plugin' ),
		'search_items'       => __( 'Search Games', 'unplayd_plugin' ),
		'parent_item_colon'  => __( 'Parent Games:', 'unplayd_plugin' ),
		'not_found'          => __( 'No games found.', 'unplayd_plugin' ),
		'not_found_in_trash' => __( 'No games found in Trash.', 'unplayd_plugin' )
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
	register_post_type( 'unplayd_plugin_games', $args );
	
	register_taxonomy(
		'unplayd_plugin_progress',
		'unplayd_plugin_games',
		array(
			'label' => __( 'Progress', 'unplayd_plugin' ),
			'rewrite' => array( 'slug' => 'progress' ),
			'hierarchical' => true,
		)
	);
    
    $unplayd_plugin_progress_presets_installed = get_option( 'unplayd_plugin_progress_presets_installed' );
	if( !$unplayd_plugin_progress_presets_installed ) {
		$progressStates = array( 'Unplayed', 'Unbeaten', 'Beaten', 'Abandoned' );
        foreach( $progressStates as $progressState ){
            if( !term_exists( $progressState, 'unplayd_plugin_progress' ) ){
                wp_insert_term( $progressState, 'unplayd_plugin_progress' );
            }
        }
		update_option( 'unplayd_plugin_progress_presets_installed', TRUE );
	}
	
	register_taxonomy(
		'unplayd_plugin_platform',
		'unplayd_plugin_games',
		array(
			'label' => __( 'Platform', 'unplayd_plugin' ),
			'rewrite' => array( 'slug' => 'platform' ),
			'hierarchical' => true,
		)
	);
	
	$unplayd_plugin_platforms_presets_installed = get_option( 'unplayd_plugin_platforms_presets_installed' );
	if( !$unplayd_plugin_platforms_presets_installed ) {
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
            if( !term_exists( $singlePlatform, 'unplayd_plugin_platform' ) ){
                wp_insert_term( $singlePlatform, 'unplayd_plugin_platform' );
            }
        }
		update_option( 'unplayd_plugin_platforms_presets_installed', TRUE );
	}
	
}
add_action( 'init', 'unplayd_plugin_init' );

/*---------------
 *
 * CSS & JS QUEUEING
 *
 *--------------*/
function unplayd_plugin_queue() {
	wp_enqueue_style( 'unplayd-fontawesome', '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css' );
	wp_enqueue_style( 'unplayd-core', plugin_dir_url( __FILE__ ) . 'assets/css/ec-unplayd.css' );
	wp_enqueue_script( 'unplayd-js', plugin_dir_url( __FILE__ ) . 'assets/js/ec-unplayd-min.js', array('jquery'), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'unplayd_plugin_queue' );

/*---------------
 *
 * METABOXES
 *
 *--------------*/
require_once('includes/metaboxes.inc.php');

/*---------------
 *
 * SHORTCODES
 *
 *--------------*/
require_once('includes/shortcodes.inc.php');

/*---------------
 *
 * WIDGET
 *
 *--------------*/
require_once('includes/widget.inc.php');