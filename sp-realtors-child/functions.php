<?php
/**
 * Child theme functions — loads after the parent theme's functions.php.
 * Only job here: enqueue the child stylesheet after the parent's main.css.
 *
 * @package SP_Realtors_Child
 */

defined( 'ABSPATH' ) || exit;

function sp_realtors_child_enqueue_styles() {
	$child_style = get_stylesheet_directory() . '/style.css';
	wp_enqueue_style(
		'sp-realtors-child-style',
		get_stylesheet_uri(),
		array( 'sp-realtors-main' ),
		file_exists( $child_style ) ? (string) filemtime( $child_style ) : null
	);
}
add_action( 'wp_enqueue_scripts', 'sp_realtors_child_enqueue_styles', 20 );
