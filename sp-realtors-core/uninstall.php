<?php
/**
 * Uninstall routine.
 *
 * Runs ONLY when the plugin is deleted from wp-admin → Plugins.
 * Data is removed ONLY if the administrator enabled
 * "Delete all data on uninstall" in Settings → SP Realtors.
 * By default nothing is deleted, so reinstalling restores everything.
 *
 * @package SP_Realtors_Core
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

$spr_settings = (array) get_option( 'spr_settings', array() );

if ( empty( $spr_settings['delete_data_on_uninstall'] ) ) {
	return;
}

// Posts (properties, testimonials, enquiries) and their meta. Media files are kept.
$spr_post_types = array( 'property', 'testimonial', 'spr_enquiry' );
foreach ( $spr_post_types as $spr_post_type ) {
	do {
		$spr_ids = get_posts(
			array(
				'post_type'        => $spr_post_type,
				'post_status'      => 'any',
				'posts_per_page'   => 200,
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'suppress_filters' => true,
			)
		);
		foreach ( $spr_ids as $spr_id ) {
			wp_delete_post( $spr_id, true );
		}
	} while ( ! empty( $spr_ids ) );
}

// Taxonomy terms. Taxonomies are not registered during uninstall, so register minimal versions first.
$spr_taxonomies = array( 'property_location', 'property_type', 'property_purpose', 'property_configuration' );
foreach ( $spr_taxonomies as $spr_taxonomy ) {
	if ( ! taxonomy_exists( $spr_taxonomy ) ) {
		register_taxonomy( $spr_taxonomy, 'property' );
	}
	$spr_terms = get_terms(
		array(
			'taxonomy'   => $spr_taxonomy,
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);
	if ( ! is_wp_error( $spr_terms ) ) {
		foreach ( $spr_terms as $spr_term_id ) {
			wp_delete_term( $spr_term_id, $spr_taxonomy );
		}
	}
}

// Options and transients.
$spr_options = array( 'spr_settings', 'spr_business', 'spr_db_version', 'spr_demo_imported', 'spr_show_demo_notice', 'spr_flush_rewrite' );
foreach ( $spr_options as $spr_option ) {
	delete_option( $spr_option );
}
delete_transient( 'spr_featured_ids' );
delete_transient( 'spr_new_enquiry_count' );
