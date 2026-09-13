<?php
/**
 * Schema.org JSON-LD: RealEstateListing (single property) + RealEstateAgent (front page).
 *
 * WHY HERE: Structured data describes the content, not the design, so it
 * must not disappear on a theme switch. Everything is filterable and can be
 * switched off in Settings → SP Realtors, and the business schema steps aside
 * automatically when a local-SEO plugin already outputs it.
 *
 * Filters: spr_enable_schema, spr_schema_listing, spr_schema_business,
 *          spr_output_business_schema
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is schema output enabled?
 *
 * @return bool
 */
function spr_schema_enabled() {
	return (bool) apply_filters( 'spr_enable_schema', (bool) spr_get_setting( 'enable_schema' ) );
}

/**
 * Detect SEO plugins that output their own LocalBusiness schema.
 *
 * @return bool
 */
function spr_seo_plugin_outputs_business_schema() {
	$detected = false;

	// Yoast Local SEO add-on.
	if ( defined( 'WPSEO_LOCAL_VERSION' ) ) {
		$detected = true;
	}

	// Rank Math with the "Local SEO" module enabled.
	if ( class_exists( '\RankMath\Helper' ) && method_exists( '\RankMath\Helper', 'is_module_active' ) && \RankMath\Helper::is_module_active( 'local-seo' ) ) {
		$detected = true;
	}

	return (bool) apply_filters( 'spr_seo_plugin_outputs_business_schema', $detected );
}

/**
 * Print a JSON-LD block.
 *
 * @param array $data Schema data.
 */
function spr_print_json_ld( $data ) {
	if ( empty( $data ) ) {
		return;
	}
	// JSON_HEX_TAG escapes "<" and ">" so the payload can never close the script tag.
	$json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP );
	if ( $json ) {
		wp_print_inline_script_tag( $json, array( 'type' => 'application/ld+json' ) );
	}
}

/**
 * Stable @id for the business entity.
 *
 * @return string
 */
function spr_schema_business_id() {
	return home_url( '/#spr-business' );
}

/**
 * RealEstateListing schema for a property.
 *
 * @param int $post_id Property ID.
 * @return array
 */
function spr_get_listing_schema( $post_id ) {
	$p = spr_get_property( $post_id );
	if ( ! $p ) {
		return array();
	}

	$post        = get_post( $post_id );
	$description = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 40, '…' );

	$images = array();
	foreach ( array_slice( $p['gallery'], 0, 6 ) as $image_id ) {
		$src = wp_get_attachment_image_url( $image_id, 'full' );
		if ( $src ) {
			$images[] = $src;
		}
	}

	$type_slug = '';
	$type_term = spr_get_first_term( $post_id, 'property_type' );
	if ( $type_term ) {
		$type_slug = $type_term->slug;
	}
	$accommodation_map = apply_filters(
		'spr_schema_accommodation_types',
		array(
			'apartment' => 'Apartment',
			'villa'     => 'House',
			'row-house' => 'House',
		)
	);
	$item_type         = isset( $accommodation_map[ $type_slug ] ) ? $accommodation_map[ $type_slug ] : 'Place';

	$address = array_filter(
		array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => spr_plain_text( $p['address'] ),
			'addressLocality' => spr_plain_text( $p['location'] ),
			'addressRegion'   => 'Maharashtra',
			'addressCountry'  => 'IN',
		)
	);

	$item = array(
		'@type'   => $item_type,
		'name'    => spr_plain_text( $p['title'] ),
		'address' => $address,
	);

	if ( $p['area'] ) {
		$unit_codes        = array(
			'sqft' => 'FTK',
			'sqm'  => 'MTK',
			'acre' => 'ACR',
		);
		$unit_key          = (string) get_post_meta( $post_id, '_spr_area_unit', true );
		$item['floorSize'] = array(
			'@type'    => 'QuantitativeValue',
			'value'    => $p['area'],
			'unitCode' => isset( $unit_codes[ $unit_key ] ) ? $unit_codes[ $unit_key ] : 'FTK',
		);
	}
	if ( 'Place' !== $item_type ) {
		if ( $p['bedrooms'] ) {
			$item['numberOfBedrooms'] = $p['bedrooms'];
		}
		if ( $p['bathrooms'] ) {
			$item['numberOfBathroomsTotal'] = $p['bathrooms'];
		}
		if ( $p['amenities'] ) {
			$item['amenityFeature'] = array();
			foreach ( $p['amenities'] as $label ) {
				$item['amenityFeature'][] = array(
					'@type' => 'LocationFeatureSpecification',
					'name'  => $label,
					'value' => true,
				);
			}
		}
	}

	$schema = array(
		'@context'      => 'https://schema.org',
		'@type'         => 'RealEstateListing',
		'@id'           => get_permalink( $post_id ) . '#listing',
		'url'           => get_permalink( $post_id ),
		'name'          => spr_plain_text( $p['title'] ),
		'description'   => spr_plain_text( $description ),
		'datePosted'    => get_the_date( 'c', $post ),
		'dateModified'  => get_the_modified_date( 'c', $post ),
		'about'         => $item,
		'provider'      => array(
			'@type' => 'RealEstateAgent',
			'@id'   => spr_schema_business_id(),
			'name'  => spr_get_business( 'name' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( $images ) {
		$schema['image'] = $images;
	}

	if ( $p['price'] ) {
		$schema['offers'] = array(
			'@type'           => 'Offer',
			'price'           => $p['price'],
			'priceCurrency'   => 'INR',
			'businessFunction' => 'rent' === $p['purpose'] ? 'http://purl.org/goodrelations/v1#LeaseOut' : 'http://purl.org/goodrelations/v1#Sell',
			'availability'    => in_array( $p['status'], array( 'sold', 'rented' ), true ) ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
		);
		if ( 'rent' === $p['purpose'] ) {
			$schema['offers']['priceSpecification'] = array(
				'@type'         => 'UnitPriceSpecification',
				'price'         => $p['price'],
				'priceCurrency' => 'INR',
				'unitCode'      => 'MON',
			);
		}
	}

	return apply_filters( 'spr_schema_listing', $schema, $post_id );
}

/**
 * RealEstateAgent (LocalBusiness) schema.
 *
 * @return array
 */
function spr_get_business_schema() {
	$info = spr_get_business();

	$same_as = array();
	foreach ( array( 'facebook', 'instagram', 'youtube', 'linkedin', 'x' ) as $network ) {
		if ( ! empty( $info[ $network ] ) ) {
			$same_as[] = esc_url_raw( $info[ $network ] );
		}
	}

	$areas = get_terms(
		array(
			'taxonomy'   => 'property_location',
			'hide_empty' => false,
			'fields'     => 'names',
			'number'     => 30,
		)
	);

	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'RealEstateAgent',
		'@id'      => spr_schema_business_id(),
		'name'     => $info['name'] ? $info['name'] : spr_plain_text( get_bloginfo( 'name' ) ),
		'url'      => home_url( '/' ),
	);

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id && wp_get_attachment_image_url( $logo_id, 'full' ) ) {
		$schema['logo']  = wp_get_attachment_image_url( $logo_id, 'full' );
		$schema['image'] = $schema['logo'];
	}
	if ( $info['phone'] ) {
		$schema['telephone'] = $info['phone'];
	}
	if ( is_email( $info['email'] ) ) {
		$schema['email'] = $info['email'];
	}
	if ( $info['address'] ) {
		$schema['address'] = array(
			'@type'          => 'PostalAddress',
			'streetAddress'  => spr_plain_text( $info['address'] ),
			'addressRegion'  => 'Maharashtra',
			'addressCountry' => 'IN',
		);
	}
	if ( ! is_wp_error( $areas ) && $areas ) {
		$schema['areaServed'] = array_map(
			function ( $name ) {
				return array(
					'@type' => 'Place',
					'name'  => spr_plain_text( $name ),
				);
			},
			$areas
		);
	}
	if ( $same_as ) {
		$schema['sameAs'] = $same_as;
	}

	return apply_filters( 'spr_schema_business', $schema );
}

/**
 * Output schema in <head>.
 */
function spr_output_schema() {
	if ( ! spr_schema_enabled() ) {
		return;
	}

	if ( is_singular( 'property' ) ) {
		spr_print_json_ld( spr_get_listing_schema( get_queried_object_id() ) );
	}

	$output_business = is_front_page() && ! spr_seo_plugin_outputs_business_schema();
	if ( apply_filters( 'spr_output_business_schema', $output_business ) ) {
		spr_print_json_ld( spr_get_business_schema() );
	}
}
add_action( 'wp_head', 'spr_output_schema', 30 );
