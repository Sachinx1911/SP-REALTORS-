<?php
/**
 * Property meta schema + registration.
 *
 * WHY HERE: One single source of truth for every property field. The meta
 * box, the save routine, the REST API and the helpers all read this schema,
 * so adding a field later means editing just this array.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta box tab groups.
 *
 * @return array key => label
 */
function spr_get_property_field_groups() {
	return apply_filters(
		'spr_property_field_groups',
		array(
			'pricing'  => __( 'Price & Status', 'sp-realtors-core' ),
			'specs'    => __( 'Specifications', 'sp-realtors-core' ),
			'location' => __( 'Address & Map', 'sp-realtors-core' ),
			'gallery'  => __( 'Photo Gallery', 'sp-realtors-core' ),
			'features' => __( 'Highlights & Amenities', 'sp-realtors-core' ),
			'legal'    => __( 'RERA & Possession', 'sp-realtors-core' ),
		)
	);
}

/**
 * Property field schema.
 *
 * input: number | text | textarea | select | checkbox | gallery | amenities | map
 *
 * @return array meta_key => field config
 */
function spr_get_property_fields() {
	$fields = array(
		'_spr_price'       => array(
			'label'       => __( 'Price (₹)', 'sp-realtors-core' ),
			'description' => __( 'Full amount in rupees, digits only. Sale: e.g. 12500000 (= ₹1.25 Cr). Rent: monthly rent, e.g. 25000.', 'sp-realtors-core' ),
			'input'       => 'number',
			'type'        => 'integer',
			'default'     => 0,
			'group'       => 'pricing',
		),
		'_spr_price_label' => array(
			'label'       => __( 'Price text (optional)', 'sp-realtors-core' ),
			'description' => __( 'Shown when Price is empty, e.g. "Price on Request".', 'sp-realtors-core' ),
			'input'       => 'text',
			'type'        => 'string',
			'default'     => '',
			'group'       => 'pricing',
		),
		'_spr_status'      => array(
			'label'   => __( 'Status', 'sp-realtors-core' ),
			'input'   => 'select',
			'type'    => 'string',
			'default' => 'ready',
			'choices' => spr_get_status_options(),
			'group'   => 'pricing',
		),
		'_spr_featured'    => array(
			'label'       => __( 'Featured property', 'sp-realtors-core' ),
			'description' => __( 'Show in "Featured Properties" on the homepage.', 'sp-realtors-core' ),
			'input'       => 'checkbox',
			'type'        => 'boolean',
			'default'     => false,
			'group'       => 'pricing',
		),
		'_spr_area'        => array(
			'label'   => __( 'Area', 'sp-realtors-core' ),
			'input'   => 'number',
			'type'    => 'integer',
			'default' => 0,
			'group'   => 'specs',
		),
		'_spr_area_unit'   => array(
			'label'   => __( 'Area unit', 'sp-realtors-core' ),
			'input'   => 'select',
			'type'    => 'string',
			'default' => 'sqft',
			'choices' => spr_get_area_units(),
			'group'   => 'specs',
		),
		'_spr_bedrooms'    => array(
			'label'   => __( 'Bedrooms', 'sp-realtors-core' ),
			'input'   => 'number',
			'type'    => 'integer',
			'default' => 0,
			'group'   => 'specs',
		),
		'_spr_bathrooms'   => array(
			'label'   => __( 'Bathrooms', 'sp-realtors-core' ),
			'input'   => 'number',
			'type'    => 'integer',
			'default' => 0,
			'group'   => 'specs',
		),
		'_spr_furnishing'  => array(
			'label'   => __( 'Furnishing', 'sp-realtors-core' ),
			'input'   => 'select',
			'type'    => 'string',
			'default' => '',
			'choices' => spr_get_furnishing_options(),
			'group'   => 'specs',
		),
		'_spr_address'     => array(
			'label'   => __( 'Address', 'sp-realtors-core' ),
			'input'   => 'textarea',
			'type'    => 'string',
			'default' => '',
			'group'   => 'location',
		),
		'_spr_map_url'     => array(
			'label'       => __( 'Google Map', 'sp-realtors-core' ),
			'description' => __( 'In Google Maps: Share → Embed a map → Copy HTML, then paste it here. Only Google Maps embeds are accepted.', 'sp-realtors-core' ),
			'input'       => 'map',
			'type'        => 'string',
			'default'     => '',
			'group'       => 'location',
		),
		'_spr_gallery'     => array(
			'label'       => __( 'Gallery images', 'sp-realtors-core' ),
			'description' => __( 'The Main Photo (featured image) is shown first. Drag to reorder.', 'sp-realtors-core' ),
			'input'       => 'gallery',
			'type'        => 'array',
			'items'       => 'integer',
			'default'     => array(),
			'group'       => 'gallery',
		),
		'_spr_highlights'  => array(
			'label'       => __( 'Highlights', 'sp-realtors-core' ),
			'description' => __( 'One highlight per line, e.g. "5 min walk to Kharghar station".', 'sp-realtors-core' ),
			'input'       => 'textarea',
			'type'        => 'string',
			'default'     => '',
			'group'       => 'features',
		),
		'_spr_amenities'   => array(
			'label'   => __( 'Amenities', 'sp-realtors-core' ),
			'input'   => 'amenities',
			'type'    => 'array',
			'items'   => 'string',
			'default' => array(),
			'choices' => spr_get_amenities_list(),
			'group'   => 'features',
		),
		'_spr_rera'        => array(
			'label'       => __( 'RERA number', 'sp-realtors-core' ),
			'description' => __( 'Leave empty to hide the RERA section.', 'sp-realtors-core' ),
			'input'       => 'text',
			'type'        => 'string',
			'default'     => '',
			'group'       => 'legal',
		),
		'_spr_possession'  => array(
			'label'       => __( 'Possession', 'sp-realtors-core' ),
			'description' => __( 'e.g. "Immediate" or "December 2027".', 'sp-realtors-core' ),
			'input'       => 'text',
			'type'        => 'string',
			'default'     => '',
			'group'       => 'legal',
		),
	);

	return apply_filters( 'spr_property_fields', $fields );
}

/**
 * Sanitize any property meta value according to its schema.
 *
 * Used as register_post_meta sanitize_callback AND by the meta box save.
 *
 * @param mixed  $value    Raw value.
 * @param string $meta_key Meta key.
 * @return mixed
 */
function spr_sanitize_property_meta( $value, $meta_key = '' ) {
	$fields = spr_get_property_fields();
	if ( ! isset( $fields[ $meta_key ] ) ) {
		return is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
	}

	$field = $fields[ $meta_key ];

	switch ( $field['input'] ) {
		case 'number':
			return is_scalar( $value ) ? absint( $value ) : 0;

		case 'textarea':
			return is_scalar( $value ) ? sanitize_textarea_field( (string) $value ) : '';

		case 'select':
			$value = is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
			return array_key_exists( $value, $field['choices'] ) ? $value : $field['default'];

		case 'checkbox':
			return (bool) rest_sanitize_boolean( $value );

		case 'gallery':
			return spr_sanitize_id_list( $value );

		case 'amenities':
			return spr_sanitize_amenities( $value );

		case 'map':
			return spr_sanitize_map_url( $value );

		case 'text':
		default:
			return is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
	}
}

/**
 * Register all property meta for the REST API / block editor.
 */
function spr_register_property_meta() {
	foreach ( spr_get_property_fields() as $key => $field ) {
		$show_in_rest = true;
		if ( 'array' === $field['type'] ) {
			$show_in_rest = array(
				'schema' => array(
					'type'  => 'array',
					'items' => array( 'type' => $field['items'] ),
				),
			);
		}

		register_post_meta(
			'property',
			$key,
			array(
				'type'              => $field['type'],
				'single'            => true,
				'default'           => $field['default'],
				'show_in_rest'      => $show_in_rest,
				'sanitize_callback' => 'spr_sanitize_property_meta',
				'auth_callback'     => 'spr_property_meta_auth',
			)
		);
	}
}
add_action( 'init', 'spr_register_property_meta', 11 );

/**
 * Only users who can edit the property may write its meta.
 *
 * @param bool   $allowed   Allowed.
 * @param string $meta_key  Meta key.
 * @param int    $object_id Post ID.
 * @return bool
 */
function spr_property_meta_auth( $allowed, $meta_key, $object_id ) {
	return current_user_can( 'edit_post', $object_id );
}
