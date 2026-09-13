<?php
/**
 * Custom Post Type: property.
 *
 * WHY HERE: Listings are content. Registering them in a theme would make
 * them disappear on a theme switch, so the CPT belongs to the plugin.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the `property` post type.
 *
 * Archive: /properties/   Single: /properties/{slug}/
 */
function spr_register_property_post_type() {
	$labels = array(
		'name'                  => _x( 'Properties', 'post type general name', 'sp-realtors-core' ),
		'singular_name'         => _x( 'Property', 'post type singular name', 'sp-realtors-core' ),
		'menu_name'             => _x( 'Properties', 'admin menu', 'sp-realtors-core' ),
		'name_admin_bar'        => _x( 'Property', 'add new on admin bar', 'sp-realtors-core' ),
		'add_new'               => __( 'Add New', 'sp-realtors-core' ),
		'add_new_item'          => __( 'Add New Property', 'sp-realtors-core' ),
		'new_item'              => __( 'New Property', 'sp-realtors-core' ),
		'edit_item'             => __( 'Edit Property', 'sp-realtors-core' ),
		'view_item'             => __( 'View Property', 'sp-realtors-core' ),
		'view_items'            => __( 'View Properties', 'sp-realtors-core' ),
		'all_items'             => __( 'All Properties', 'sp-realtors-core' ),
		'search_items'          => __( 'Search Properties', 'sp-realtors-core' ),
		'not_found'             => __( 'No properties found.', 'sp-realtors-core' ),
		'not_found_in_trash'    => __( 'No properties found in Trash.', 'sp-realtors-core' ),
		'featured_image'        => __( 'Main Photo', 'sp-realtors-core' ),
		'set_featured_image'    => __( 'Set main photo', 'sp-realtors-core' ),
		'remove_featured_image' => __( 'Remove main photo', 'sp-realtors-core' ),
		'use_featured_image'    => __( 'Use as main photo', 'sp-realtors-core' ),
		'archives'              => __( 'Property Archives', 'sp-realtors-core' ),
		'attributes'            => __( 'Property Attributes', 'sp-realtors-core' ),
		'insert_into_item'      => __( 'Insert into property', 'sp-realtors-core' ),
		'uploaded_to_this_item' => __( 'Uploaded to this property', 'sp-realtors-core' ),
		'filter_items_list'     => __( 'Filter properties list', 'sp-realtors-core' ),
		'items_list_navigation' => __( 'Properties list navigation', 'sp-realtors-core' ),
		'items_list'            => __( 'Properties list', 'sp-realtors-core' ),
		'item_published'        => __( 'Property published.', 'sp-realtors-core' ),
		'item_updated'          => __( 'Property updated.', 'sp-realtors-core' ),
	);

	$args = array(
		'labels'          => $labels,
		'description'     => __( 'Real-estate listings.', 'sp-realtors-core' ),
		'public'          => true,
		'show_in_rest'    => true,
		'menu_position'   => 5,
		'menu_icon'       => 'dashicons-building',
		'capability_type' => 'post',
		'map_meta_cap'    => true,
		'hierarchical'    => false,
		'supports'        => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields', 'author' ),
		'has_archive'     => true,
		'rewrite'         => array(
			'slug'       => 'properties',
			'with_front' => false,
		),
		'query_var'       => true,
		'taxonomies'      => spr_get_property_taxonomies(),
	);

	register_post_type( 'property', apply_filters( 'spr_property_post_type_args', $args ) );
}
add_action( 'init', 'spr_register_property_post_type' );

/**
 * Friendly placeholder in the title field.
 *
 * @param string  $text Placeholder.
 * @param WP_Post $post Post.
 * @return string
 */
function spr_property_title_placeholder( $text, $post ) {
	if ( 'property' === $post->post_type ) {
		return __( 'e.g. 2 BHK Apartment in Sector 20, Kharghar', 'sp-realtors-core' );
	}
	return $text;
}
add_filter( 'enter_title_here', 'spr_property_title_placeholder', 10, 2 );

/**
 * Property-specific "updated" messages.
 *
 * @param array $messages Messages.
 * @return array
 */
function spr_property_updated_messages( $messages ) {
	$post = get_post();
	if ( ! $post || 'property' !== $post->post_type ) {
		return $messages;
	}

	$link = sprintf( ' <a href="%s">%s</a>', esc_url( get_permalink( $post ) ), esc_html__( 'View property', 'sp-realtors-core' ) );

	$messages['property'] = array(
		0  => '',
		1  => esc_html__( 'Property updated.', 'sp-realtors-core' ) . $link,
		4  => esc_html__( 'Property updated.', 'sp-realtors-core' ),
		6  => esc_html__( 'Property published.', 'sp-realtors-core' ) . $link,
		7  => esc_html__( 'Property saved.', 'sp-realtors-core' ),
		8  => esc_html__( 'Property submitted.', 'sp-realtors-core' ),
		9  => esc_html__( 'Property scheduled.', 'sp-realtors-core' ),
		10 => esc_html__( 'Property draft updated.', 'sp-realtors-core' ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'spr_property_updated_messages' );
