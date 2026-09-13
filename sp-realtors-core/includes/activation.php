<?php
/**
 * Activation, deactivation and upgrade routines.
 *
 * WHY HERE: Seeding terms and flushing rewrite rules are one-time data
 * operations tied to the plugin lifecycle. Deactivation NEVER deletes data.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default taxonomy terms seeded on activation (only if they do not exist).
 *
 * @return array taxonomy => [ slug => name ]
 */
function spr_default_terms() {
	return apply_filters(
		'spr_default_terms',
		array(
			'property_location'      => array(
				'kharghar'    => 'Kharghar',
				'panvel'      => 'Panvel',
				'ulwe'        => 'Ulwe',
				'vashi'       => 'Vashi',
				'nerul'       => 'Nerul',
				'cbd-belapur' => 'CBD Belapur',
				'seawoods'    => 'Seawoods',
				'kamothe'     => 'Kamothe',
				'taloja'      => 'Taloja',
				'airoli'      => 'Airoli',
				'ghansoli'    => 'Ghansoli',
				'sanpada'     => 'Sanpada',
			),
			'property_type'          => array(
				'apartment' => __( 'Apartment', 'sp-realtors-core' ),
				'villa'     => __( 'Villa / Bungalow', 'sp-realtors-core' ),
				'row-house' => __( 'Row House', 'sp-realtors-core' ),
				'plot'      => __( 'Plot', 'sp-realtors-core' ),
				'shop'      => __( 'Shop', 'sp-realtors-core' ),
				'office'    => __( 'Office', 'sp-realtors-core' ),
			),
			'property_purpose'       => array(
				'buy'  => __( 'Buy', 'sp-realtors-core' ),
				'rent' => __( 'Rent', 'sp-realtors-core' ),
			),
			'property_configuration' => array(
				'1-rk'       => '1 RK',
				'1-bhk'      => '1 BHK',
				'2-bhk'      => '2 BHK',
				'3-bhk'      => '3 BHK',
				'4-bhk-plus' => '4+ BHK',
			),
		)
	);
}

/**
 * Insert missing default terms.
 */
function spr_seed_default_terms() {
	foreach ( spr_default_terms() as $taxonomy => $terms ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}
		foreach ( $terms as $slug => $name ) {
			if ( ! term_exists( $slug, $taxonomy ) ) {
				wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
			}
		}
	}
}

/**
 * Add default options without overwriting existing ones.
 */
function spr_add_default_options() {
	add_option(
		'spr_settings',
		array(
			'enquiry_email'            => get_option( 'admin_email' ),
			'enable_schema'            => 1,
			'delete_data_on_uninstall' => 0,
		)
	);

	add_option(
		'spr_business',
		array(
			'name'      => 'SP REALTORS',
			'phone'     => '',
			'whatsapp'  => '',
			'email'     => get_option( 'admin_email' ),
			'address'   => 'Navi Mumbai, Maharashtra',
			'hours'     => 'Mon – Sat: 10:00 AM – 7:00 PM',
			'facebook'  => '',
			'instagram' => '',
			'youtube'   => '',
			'linkedin'  => '',
			'x'         => '',
		)
	);
}

/**
 * Activation hook.
 */
function spr_activate() {
	spr_register_taxonomies();
	spr_register_property_post_type();
	spr_register_testimonial_post_type();
	spr_register_enquiry_post_type();

	spr_seed_default_terms();
	spr_add_default_options();

	if ( ! get_option( 'spr_demo_imported' ) ) {
		update_option( 'spr_show_demo_notice', 1, false );
	}
	update_option( 'spr_db_version', SPR_VERSION );

	flush_rewrite_rules();
}

/**
 * Deactivation hook: only refresh permalinks. Data is never deleted here.
 */
function spr_deactivate() {
	flush_rewrite_rules();
}

/**
 * Run upgrade tasks when plugin files are updated without re-activation (e.g. FTP upload).
 */
function spr_maybe_upgrade() {
	if ( get_option( 'spr_db_version' ) === SPR_VERSION ) {
		return;
	}
	spr_seed_default_terms();
	spr_add_default_options();
	update_option( 'spr_db_version', SPR_VERSION );
	update_option( 'spr_flush_rewrite', 1, false );
}
add_action( 'init', 'spr_maybe_upgrade', 20 );

/**
 * Deferred rewrite flush (runs once, after all rewrite rules are registered).
 */
function spr_maybe_flush_rewrite() {
	if ( get_option( 'spr_flush_rewrite' ) ) {
		delete_option( 'spr_flush_rewrite' );
		flush_rewrite_rules();
	}
}
add_action( 'init', 'spr_maybe_flush_rewrite', 99 );
