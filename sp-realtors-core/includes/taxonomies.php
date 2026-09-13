<?php
/**
 * Property taxonomies: location, type, purpose, configuration.
 *
 * WHY HERE: Categories/locations are content structure. New location term
 * => new "Areas We Serve" card automatically, in any theme.
 *
 * `query_var` is false on purpose: the archive filters use our own query vars
 * (location, ptype, purpose, config) that accept arrays for checkbox filters.
 * Term archive URLs (/location/kharghar/) keep working through rewrite rules.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register all property taxonomies.
 */
function spr_register_taxonomies() {
	$common = array(
		'public'            => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => false,
		'query_var'         => false,
	);

	$taxonomies = array(
		'property_location'      => array(
			'hierarchical' => true,
			'rewrite'      => array(
				'slug'         => 'location',
				'with_front'   => false,
				'hierarchical' => true,
			),
			'labels'       => array(
				'name'              => _x( 'Locations', 'taxonomy general name', 'sp-realtors-core' ),
				'singular_name'     => _x( 'Location', 'taxonomy singular name', 'sp-realtors-core' ),
				'menu_name'         => __( 'Locations', 'sp-realtors-core' ),
				'search_items'      => __( 'Search Locations', 'sp-realtors-core' ),
				'all_items'         => __( 'All Locations', 'sp-realtors-core' ),
				'parent_item'       => __( 'Parent Location', 'sp-realtors-core' ),
				'parent_item_colon' => __( 'Parent Location:', 'sp-realtors-core' ),
				'edit_item'         => __( 'Edit Location', 'sp-realtors-core' ),
				'view_item'         => __( 'View Location', 'sp-realtors-core' ),
				'update_item'       => __( 'Update Location', 'sp-realtors-core' ),
				'add_new_item'      => __( 'Add New Location', 'sp-realtors-core' ),
				'new_item_name'     => __( 'New Location Name', 'sp-realtors-core' ),
				'not_found'         => __( 'No locations found.', 'sp-realtors-core' ),
				'back_to_items'     => __( '&larr; Back to Locations', 'sp-realtors-core' ),
			),
		),
		'property_type'          => array(
			'hierarchical' => true,
			'rewrite'      => array(
				'slug'       => 'property-type',
				'with_front' => false,
			),
			'labels'       => array(
				'name'              => _x( 'Property Types', 'taxonomy general name', 'sp-realtors-core' ),
				'singular_name'     => _x( 'Property Type', 'taxonomy singular name', 'sp-realtors-core' ),
				'menu_name'         => __( 'Types', 'sp-realtors-core' ),
				'search_items'      => __( 'Search Types', 'sp-realtors-core' ),
				'all_items'         => __( 'All Types', 'sp-realtors-core' ),
				'parent_item'       => __( 'Parent Type', 'sp-realtors-core' ),
				'parent_item_colon' => __( 'Parent Type:', 'sp-realtors-core' ),
				'edit_item'         => __( 'Edit Type', 'sp-realtors-core' ),
				'view_item'         => __( 'View Type', 'sp-realtors-core' ),
				'update_item'       => __( 'Update Type', 'sp-realtors-core' ),
				'add_new_item'      => __( 'Add New Type', 'sp-realtors-core' ),
				'new_item_name'     => __( 'New Type Name', 'sp-realtors-core' ),
				'not_found'         => __( 'No types found.', 'sp-realtors-core' ),
				'back_to_items'     => __( '&larr; Back to Types', 'sp-realtors-core' ),
			),
		),
		// Purpose & Configuration are "hierarchical" only to get checkbox UI in both
		// the block and classic editors (no free-text typos like "2bhk" vs "2 BHK").
		'property_purpose'       => array(
			'hierarchical' => true,
			'rewrite'      => array(
				'slug'       => 'purpose',
				'with_front' => false,
			),
			'labels'       => array(
				'name'          => _x( 'Purposes', 'taxonomy general name', 'sp-realtors-core' ),
				'singular_name' => _x( 'Purpose', 'taxonomy singular name', 'sp-realtors-core' ),
				'menu_name'     => __( 'Purpose (Buy/Rent)', 'sp-realtors-core' ),
				'search_items'  => __( 'Search Purposes', 'sp-realtors-core' ),
				'all_items'     => __( 'All Purposes', 'sp-realtors-core' ),
				'edit_item'     => __( 'Edit Purpose', 'sp-realtors-core' ),
				'view_item'     => __( 'View Purpose', 'sp-realtors-core' ),
				'update_item'   => __( 'Update Purpose', 'sp-realtors-core' ),
				'add_new_item'  => __( 'Add New Purpose', 'sp-realtors-core' ),
				'new_item_name' => __( 'New Purpose Name', 'sp-realtors-core' ),
				'not_found'     => __( 'No purposes found.', 'sp-realtors-core' ),
				'back_to_items' => __( '&larr; Back to Purposes', 'sp-realtors-core' ),
			),
		),
		'property_configuration' => array(
			'hierarchical' => true,
			'rewrite'      => array(
				'slug'       => 'configuration',
				'with_front' => false,
			),
			'labels'       => array(
				'name'          => _x( 'Configurations', 'taxonomy general name', 'sp-realtors-core' ),
				'singular_name' => _x( 'Configuration', 'taxonomy singular name', 'sp-realtors-core' ),
				'menu_name'     => __( 'Configurations (BHK)', 'sp-realtors-core' ),
				'search_items'  => __( 'Search Configurations', 'sp-realtors-core' ),
				'all_items'     => __( 'All Configurations', 'sp-realtors-core' ),
				'edit_item'     => __( 'Edit Configuration', 'sp-realtors-core' ),
				'view_item'     => __( 'View Configuration', 'sp-realtors-core' ),
				'update_item'   => __( 'Update Configuration', 'sp-realtors-core' ),
				'add_new_item'  => __( 'Add New Configuration', 'sp-realtors-core' ),
				'new_item_name' => __( 'New Configuration Name', 'sp-realtors-core' ),
				'not_found'     => __( 'No configurations found.', 'sp-realtors-core' ),
				'back_to_items' => __( '&larr; Back to Configurations', 'sp-realtors-core' ),
			),
		),
	);

	foreach ( $taxonomies as $taxonomy => $args ) {
		$args = apply_filters( 'spr_taxonomy_args', array_merge( $common, $args ), $taxonomy );
		register_taxonomy( $taxonomy, array( 'property' ), $args );
	}
}
// Priority 5: taxonomies exist before the post type (priority 10) references them.
add_action( 'init', 'spr_register_taxonomies', 5 );
