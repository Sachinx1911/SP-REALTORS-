<?php
/**
 * Public helper API.
 *
 * WHY HERE: These functions expose listing data in a stable, escaped-ready
 * shape. The theme calls ONLY these (guarded by function_exists), so the
 * theme never needs to know meta keys or taxonomy internals.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Choice lists (filterable so a child plugin can extend them).
 * ---------------------------------------------------------------------- */

/**
 * Property taxonomies handled by this plugin.
 *
 * @return string[]
 */
function spr_get_property_taxonomies() {
	return array( 'property_location', 'property_type', 'property_purpose', 'property_configuration' );
}

/**
 * Area units.
 *
 * @return array key => label
 */
function spr_get_area_units() {
	return apply_filters(
		'spr_area_units',
		array(
			'sqft' => __( 'sq.ft', 'sp-realtors-core' ),
			'sqm'  => __( 'sq.m', 'sp-realtors-core' ),
			'acre' => __( 'acre', 'sp-realtors-core' ),
		)
	);
}

/**
 * Furnishing options.
 *
 * @return array key => label
 */
function spr_get_furnishing_options() {
	return apply_filters(
		'spr_furnishing_options',
		array(
			''            => __( '— Not specified —', 'sp-realtors-core' ),
			'unfurnished' => __( 'Unfurnished', 'sp-realtors-core' ),
			'semi'        => __( 'Semi-Furnished', 'sp-realtors-core' ),
			'fully'       => __( 'Fully Furnished', 'sp-realtors-core' ),
		)
	);
}

/**
 * Listing status options (shown as the card badge).
 *
 * @return array key => label
 */
function spr_get_status_options() {
	return apply_filters(
		'spr_status_options',
		array(
			'ready'              => __( 'Ready to Move', 'sp-realtors-core' ),
			'under-construction' => __( 'Under Construction', 'sp-realtors-core' ),
			'new-launch'         => __( 'New Launch', 'sp-realtors-core' ),
			'resale'             => __( 'Resale', 'sp-realtors-core' ),
			'sold'               => __( 'Sold', 'sp-realtors-core' ),
			'rented'             => __( 'Rented', 'sp-realtors-core' ),
		)
	);
}

/**
 * Amenities list.
 *
 * @return array key => label
 */
function spr_get_amenities_list() {
	return apply_filters(
		'spr_amenities_list',
		array(
			'lift'         => __( 'Lift', 'sp-realtors-core' ),
			'parking'      => __( 'Parking', 'sp-realtors-core' ),
			'gym'          => __( 'Gym', 'sp-realtors-core' ),
			'pool'         => __( 'Swimming Pool', 'sp-realtors-core' ),
			'security'     => __( '24x7 Security', 'sp-realtors-core' ),
			'power-backup' => __( 'Power Backup', 'sp-realtors-core' ),
			'garden'       => __( 'Garden', 'sp-realtors-core' ),
			'clubhouse'    => __( 'Clubhouse', 'sp-realtors-core' ),
			'play-area'    => __( 'Children\'s Play Area', 'sp-realtors-core' ),
			'cctv'         => __( 'CCTV', 'sp-realtors-core' ),
			'gas-pipeline' => __( 'Gas Pipeline', 'sp-realtors-core' ),
			'intercom'     => __( 'Intercom', 'sp-realtors-core' ),
		)
	);
}

/**
 * Budget ranges used by the search card and archive filters.
 *
 * @param string $purpose 'buy', 'rent' or '' for all.
 * @return array key => [ 'label' => string, 'min' => int, 'max' => int (0 = no max), 'purpose' => string ]
 */
function spr_get_budget_ranges( $purpose = '' ) {
	$ranges = array(
		'under-50l' => array(
			'label'   => __( 'Under ₹50 Lakh', 'sp-realtors-core' ),
			'min'     => 0,
			'max'     => 5000000,
			'purpose' => 'buy',
		),
		'50l-1cr'   => array(
			'label'   => __( '₹50 Lakh – ₹1 Cr', 'sp-realtors-core' ),
			'min'     => 5000000,
			'max'     => 10000000,
			'purpose' => 'buy',
		),
		'1cr-2cr'   => array(
			'label'   => __( '₹1 Cr – ₹2 Cr', 'sp-realtors-core' ),
			'min'     => 10000000,
			'max'     => 20000000,
			'purpose' => 'buy',
		),
		'above-2cr' => array(
			'label'   => __( 'Above ₹2 Cr', 'sp-realtors-core' ),
			'min'     => 20000000,
			'max'     => 0,
			'purpose' => 'buy',
		),
		'under-20k' => array(
			'label'   => __( 'Under ₹20,000/month', 'sp-realtors-core' ),
			'min'     => 0,
			'max'     => 20000,
			'purpose' => 'rent',
		),
		'20k-50k'   => array(
			'label'   => __( '₹20,000 – ₹50,000/month', 'sp-realtors-core' ),
			'min'     => 20000,
			'max'     => 50000,
			'purpose' => 'rent',
		),
		'above-50k' => array(
			'label'   => __( 'Above ₹50,000/month', 'sp-realtors-core' ),
			'min'     => 50000,
			'max'     => 0,
			'purpose' => 'rent',
		),
	);

	$ranges = apply_filters( 'spr_budget_ranges', $ranges );

	if ( '' === $purpose ) {
		return $ranges;
	}

	return array_filter(
		$ranges,
		function ( $range ) use ( $purpose ) {
			return isset( $range['purpose'] ) && $range['purpose'] === $purpose;
		}
	);
}

/* -------------------------------------------------------------------------
 * Sanitizers.
 * ---------------------------------------------------------------------- */

/**
 * Sanitize a list of image attachment IDs (array or comma-separated string).
 *
 * @param mixed $value Raw value.
 * @return int[]
 */
function spr_sanitize_id_list( $value ) {
	if ( is_string( $value ) ) {
		$value = explode( ',', $value );
	}
	if ( ! is_array( $value ) ) {
		return array();
	}

	$ids = array();
	foreach ( $value as $id ) {
		$id = absint( $id );
		if ( $id && wp_attachment_is_image( $id ) && ! in_array( $id, $ids, true ) ) {
			$ids[] = $id;
		}
		if ( count( $ids ) >= 50 ) {
			break;
		}
	}
	return $ids;
}

/**
 * Sanitize amenities against the allow-list.
 *
 * @param mixed $value Raw value.
 * @return string[]
 */
function spr_sanitize_amenities( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}
	$allowed = array_keys( spr_get_amenities_list() );
	$clean   = array();
	foreach ( $value as $item ) {
		$item = sanitize_key( (string) $item );
		if ( in_array( $item, $allowed, true ) && ! in_array( $item, $clean, true ) ) {
			$clean[] = $item;
		}
	}
	return $clean;
}

/**
 * Sanitize a Google Maps embed URL.
 *
 * Accepts either the plain embed URL or the full <iframe> code copied from
 * Google Maps. Only allow-listed Google hosts and embed paths are kept, so
 * no arbitrary iframe can ever be injected.
 *
 * @param mixed $value Raw value.
 * @return string Clean https URL or ''.
 */
function spr_sanitize_map_url( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	if ( false !== stripos( $value, '<iframe' ) && preg_match( '/\bsrc\s*=\s*["\']([^"\']+)["\']/i', $value, $m ) ) {
		$value = html_entity_decode( $m[1], ENT_QUOTES, 'UTF-8' );
	}

	$url = esc_url_raw( $value, array( 'https' ) );
	if ( '' === $url ) {
		return '';
	}

	$parts = wp_parse_url( $url );
	$host  = isset( $parts['host'] ) ? strtolower( $parts['host'] ) : '';
	$path  = isset( $parts['path'] ) ? $parts['path'] : '';
	$query = isset( $parts['query'] ) ? $parts['query'] : '';

	$allowed_hosts = apply_filters( 'spr_map_allowed_hosts', array( 'www.google.com', 'google.com', 'maps.google.com' ) );
	if ( ! in_array( $host, $allowed_hosts, true ) ) {
		return '';
	}

	$is_embed_path   = 0 === strpos( $path, '/maps/embed' );
	$is_output_embed = ( '/maps' === $path || 0 === strpos( $path, '/maps/' ) ) && false !== strpos( $query, 'output=embed' );

	return ( $is_embed_path || $is_output_embed ) ? $url : '';
}

/* -------------------------------------------------------------------------
 * Formatting.
 * ---------------------------------------------------------------------- */

/**
 * Format an integer with Indian digit grouping (12,34,567).
 *
 * @param int $number Number.
 * @return string
 */
function spr_number_format_indian( $number ) {
	$number = (string) absint( $number );
	if ( strlen( $number ) <= 3 ) {
		return $number;
	}
	$last3 = substr( $number, -3 );
	$rest  = substr( $number, 0, strlen( $number ) - 3 );
	$rest  = preg_replace( '/\B(?=(\d{2})+(?!\d))/', ',', $rest );
	return $rest . ',' . $last3;
}

/**
 * Round to at most 2 decimals, dropping trailing zeros (1.50 → 1.5, 2.00 → 2).
 *
 * @param float $value Value.
 * @return string
 */
function spr_short_decimal( $value ) {
	$value = round( (float) $value, 2 );
	if ( (float) floor( $value ) === $value ) {
		$decimals = 0;
	} elseif ( round( $value, 1 ) === $value ) {
		$decimals = 1;
	} else {
		$decimals = 2;
	}
	return number_format_i18n( $value, $decimals );
}

/**
 * Human price: ₹1.25 Cr, ₹45 L, ₹25,000 (+ "/month" for rent).
 *
 * @param int    $amount  Price in rupees.
 * @param string $purpose 'buy' or 'rent'.
 * @return string Plain text (escape on output).
 */
function spr_format_price( $amount, $purpose = 'buy' ) {
	$amount = absint( $amount );
	if ( ! $amount ) {
		return '';
	}

	if ( $amount >= 10000000 ) {
		/* translators: %s: amount in crores, e.g. 1.25 */
		$formatted = sprintf( __( '₹%s Cr', 'sp-realtors-core' ), spr_short_decimal( $amount / 10000000 ) );
	} elseif ( $amount >= 100000 ) {
		/* translators: %s: amount in lakhs, e.g. 45 */
		$formatted = sprintf( __( '₹%s L', 'sp-realtors-core' ), spr_short_decimal( $amount / 100000 ) );
	} else {
		$formatted = '₹' . spr_number_format_indian( $amount );
	}

	if ( 'rent' === $purpose ) {
		/* translators: %s: formatted rent amount */
		$formatted = sprintf( __( '%s/month', 'sp-realtors-core' ), $formatted );
	}

	return apply_filters( 'spr_format_price', $formatted, $amount, $purpose );
}

/**
 * Decode a WordPress-stored title/term name to plain text (for messages, JSON, attributes).
 *
 * @param string $text Text possibly containing HTML entities.
 * @return string
 */
function spr_plain_text( $text ) {
	return trim( wp_strip_all_tags( html_entity_decode( (string) $text, ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );
}

/* -------------------------------------------------------------------------
 * Data getters.
 * ---------------------------------------------------------------------- */

/**
 * First term of a taxonomy for a post.
 *
 * @param int    $post_id  Post ID.
 * @param string $taxonomy Taxonomy.
 * @return WP_Term|null
 */
function spr_get_first_term( $post_id, $taxonomy ) {
	$terms = get_the_terms( $post_id, $taxonomy );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return null;
	}
	return reset( $terms );
}

/**
 * Everything about a property in one array.
 *
 * Values are RAW (unescaped). Always escape on output.
 *
 * @param int|WP_Post|null $post Post or ID (defaults to current post).
 * @return array Empty array if not a property.
 */
function spr_get_property( $post = null ) {
	static $cache = array();

	$post = get_post( $post );
	if ( ! $post || 'property' !== $post->post_type ) {
		return array();
	}
	if ( isset( $cache[ $post->ID ] ) ) {
		return $cache[ $post->ID ];
	}

	$id       = $post->ID;
	$purpose  = spr_get_first_term( $id, 'property_purpose' );
	$location = spr_get_first_term( $id, 'property_location' );
	$type     = spr_get_first_term( $id, 'property_type' );
	$config   = spr_get_first_term( $id, 'property_configuration' );

	$purpose_slug = $purpose ? $purpose->slug : 'buy';
	$price        = absint( get_post_meta( $id, '_spr_price', true ) );
	$price_label  = (string) get_post_meta( $id, '_spr_price_label', true );

	$units       = spr_get_area_units();
	$area_unit   = (string) get_post_meta( $id, '_spr_area_unit', true );
	$furnishings = spr_get_furnishing_options();
	$furnishing  = (string) get_post_meta( $id, '_spr_furnishing', true );
	$statuses    = spr_get_status_options();
	$status      = (string) get_post_meta( $id, '_spr_status', true );

	$amenity_list = spr_get_amenities_list();
	$amenities    = array();
	foreach ( (array) get_post_meta( $id, '_spr_amenities', true ) as $key ) {
		if ( isset( $amenity_list[ $key ] ) ) {
			$amenities[ $key ] = $amenity_list[ $key ];
		}
	}

	$highlights = array_values(
		array_filter(
			array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) get_post_meta( $id, '_spr_highlights', true ) ) )
		)
	);

	$price_formatted = $price ? spr_format_price( $price, $purpose_slug ) : '';
	if ( '' === $price_formatted ) {
		$price_formatted = '' !== $price_label ? $price_label : __( 'Price on Request', 'sp-realtors-core' );
	}

	$data = array(
		'id'                => $id,
		'title'             => get_the_title( $id ),
		'permalink'         => get_permalink( $id ),
		'thumbnail_id'      => (int) get_post_thumbnail_id( $id ),
		'price'             => $price,
		'price_formatted'   => $price_formatted,
		'purpose'           => $purpose_slug,
		'purpose_label'     => $purpose ? $purpose->name : '',
		'location'          => $location ? $location->name : '',
		'location_term'     => $location,
		'type'              => $type ? $type->name : '',
		'configuration'     => $config ? $config->name : '',
		'area'              => absint( get_post_meta( $id, '_spr_area', true ) ),
		'area_unit'         => isset( $units[ $area_unit ] ) ? $units[ $area_unit ] : $units['sqft'],
		'bedrooms'          => absint( get_post_meta( $id, '_spr_bedrooms', true ) ),
		'bathrooms'         => absint( get_post_meta( $id, '_spr_bathrooms', true ) ),
		'furnishing'        => $furnishing,
		'furnishing_label'  => ( '' !== $furnishing && isset( $furnishings[ $furnishing ] ) ) ? $furnishings[ $furnishing ] : '',
		'status'            => $status,
		'status_label'      => isset( $statuses[ $status ] ) ? $statuses[ $status ] : '',
		'gallery'           => spr_get_gallery_ids( $id ),
		'address'           => (string) get_post_meta( $id, '_spr_address', true ),
		'map_url'           => (string) get_post_meta( $id, '_spr_map_url', true ),
		'rera'              => (string) get_post_meta( $id, '_spr_rera', true ),
		'possession'        => (string) get_post_meta( $id, '_spr_possession', true ),
		'highlights'        => $highlights,
		'amenities'         => $amenities,
		'featured'          => (bool) get_post_meta( $id, '_spr_featured', true ),
		'whatsapp_url'      => spr_property_whatsapp_url( $id ),
	);

	$cache[ $id ] = apply_filters( 'spr_property_data', $data, $post );
	return $cache[ $id ];
}

/**
 * Featured image + gallery images, de-duplicated, in order.
 *
 * @param int $post_id Post ID.
 * @return int[]
 */
function spr_get_gallery_ids( $post_id ) {
	$ids   = array();
	$thumb = (int) get_post_thumbnail_id( $post_id );
	if ( $thumb ) {
		$ids[] = $thumb;
	}
	foreach ( (array) get_post_meta( $post_id, '_spr_gallery', true ) as $id ) {
		$id = absint( $id );
		if ( $id && ! in_array( $id, $ids, true ) ) {
			$ids[] = $id;
		}
	}
	return $ids;
}

/**
 * Business / contact info (stored as an option, not a theme_mod, so it survives theme changes).
 *
 * @param string $key Optional key. Empty returns the full array.
 * @return string|array
 */
function spr_get_business( $key = '' ) {
	$defaults = array(
		'name'      => 'SP REALTORS',
		'phone'     => '',
		'whatsapp'  => '',
		'email'     => '',
		'address'   => '',
		'hours'     => '',
		'facebook'  => '',
		'instagram' => '',
		'youtube'   => '',
		'linkedin'  => '',
		'x'         => '',
	);
	$info = wp_parse_args( (array) get_option( 'spr_business', array() ), $defaults );
	$info = apply_filters( 'spr_business_info', $info );

	if ( '' === $key ) {
		return $info;
	}
	return isset( $info[ $key ] ) ? (string) $info[ $key ] : '';
}

/**
 * Plugin settings with defaults.
 *
 * @param string $key Optional key.
 * @return mixed
 */
function spr_get_setting( $key = '' ) {
	$defaults = array(
		'enquiry_email'            => get_option( 'admin_email' ),
		'enable_schema'            => 1,
		'delete_data_on_uninstall' => 0,
	);
	$settings = wp_parse_args( (array) get_option( 'spr_settings', array() ), $defaults );

	if ( '' === $key ) {
		return $settings;
	}
	return isset( $settings[ $key ] ) ? $settings[ $key ] : null;
}

/* -------------------------------------------------------------------------
 * Links.
 * ---------------------------------------------------------------------- */

/**
 * WhatsApp click-to-chat URL.
 *
 * @param string $message Optional pre-filled message (plain text).
 * @return string URL (escape with esc_url on output) or '' if no number set.
 */
function spr_whatsapp_url( $message = '' ) {
	$digits = preg_replace( '/\D+/', '', spr_get_business( 'whatsapp' ) );
	if ( '' === $digits ) {
		return '';
	}
	$url = 'https://wa.me/' . $digits;
	if ( '' !== $message ) {
		$url = add_query_arg( 'text', rawurlencode( $message ), $url );
	}
	return $url;
}

/**
 * WhatsApp enquiry URL for a specific property.
 *
 * @param int $post_id Property ID.
 * @return string
 */
function spr_property_whatsapp_url( $post_id ) {
	$title    = spr_plain_text( get_the_title( $post_id ) );
	$location = spr_get_first_term( $post_id, 'property_location' );

	if ( $location ) {
		/* translators: 1: property title, 2: location name */
		$message = sprintf( __( 'Hello SP REALTORS, I am interested in %1$s in %2$s.', 'sp-realtors-core' ), $title, spr_plain_text( $location->name ) );
	} else {
		/* translators: %s: property title */
		$message = sprintf( __( 'Hello SP REALTORS, I am interested in %s.', 'sp-realtors-core' ), $title );
	}

	return spr_whatsapp_url( apply_filters( 'spr_property_whatsapp_message', $message, $post_id ) );
}

/**
 * tel: URL from the business phone.
 *
 * @return string
 */
function spr_tel_url() {
	$phone = preg_replace( '/[^\d+]/', '', spr_get_business( 'phone' ) );
	return '' !== $phone ? 'tel:' . $phone : '';
}

/* -------------------------------------------------------------------------
 * Queries.
 * ---------------------------------------------------------------------- */

/**
 * IDs of published featured properties (cached in a transient).
 *
 * @return int[]
 */
function spr_get_featured_property_ids() {
	$ids = get_transient( 'spr_featured_ids' );
	if ( false === $ids ) {
		$ids = get_posts(
			array(
				'post_type'        => 'property',
				'post_status'      => 'publish',
				'posts_per_page'   => 12,
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'suppress_filters' => false,
				'meta_key'         => '_spr_featured', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'       => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		set_transient( 'spr_featured_ids', $ids, DAY_IN_SECONDS );
	}
	return array_map( 'absint', (array) $ids );
}

/**
 * Query for featured properties; tops up with latest listings if not enough are flagged.
 *
 * @param int $count Number of properties.
 * @return WP_Query
 */
function spr_featured_properties_query( $count = 3 ) {
	$count = max( 1, absint( $count ) );
	$ids   = array_slice( spr_get_featured_property_ids(), 0, $count );

	if ( count( $ids ) < $count ) {
		$latest = get_posts(
			array(
				'post_type'        => 'property',
				'post_status'      => 'publish',
				'posts_per_page'   => $count - count( $ids ),
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'post__not_in'     => $ids, // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
				'suppress_filters' => false,
			)
		);
		$ids = array_merge( $ids, array_map( 'absint', $latest ) );
	}

	if ( empty( $ids ) ) {
		$ids = array( 0 ); // Forces an empty result instead of "all posts".
	}

	return new WP_Query(
		array(
			'post_type'           => 'property',
			'post_status'         => 'publish',
			'post__in'            => $ids,
			'orderby'             => 'post__in',
			'posts_per_page'      => $count,
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
		)
	);
}

/**
 * Related properties sharing a location.
 *
 * @param int $post_id Property ID.
 * @param int $count   Number of properties.
 * @return WP_Query
 */
function spr_related_properties_query( $post_id, $count = 3 ) {
	$args = array(
		'post_type'           => 'property',
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, absint( $count ) ),
		'post__not_in'        => array( absint( $post_id ) ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	);

	$terms = wp_get_post_terms( $post_id, 'property_location', array( 'fields' => 'ids' ) );
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'property_location',
				'field'    => 'term_id',
				'terms'    => $terms,
			),
		);
	}

	return new WP_Query( apply_filters( 'spr_related_properties_args', $args, $post_id ) );
}

/**
 * Clear listing caches when a property changes.
 *
 * @param int $post_id Post ID.
 */
function spr_flush_property_cache( $post_id = 0 ) {
	if ( $post_id && 'property' !== get_post_type( $post_id ) ) {
		return;
	}
	delete_transient( 'spr_featured_ids' );
}
add_action( 'save_post_property', 'spr_flush_property_cache' );
add_action( 'trashed_post', 'spr_flush_property_cache' );
add_action( 'untrashed_post', 'spr_flush_property_cache' );
add_action( 'deleted_post', 'spr_flush_property_cache' );

/* -------------------------------------------------------------------------
 * Output helpers.
 * ---------------------------------------------------------------------- */

/**
 * Safe Google Maps iframe for a property (or any stored embed URL).
 *
 * @param int|string $source Property ID or embed URL.
 * @param string     $title  Accessible iframe title.
 * @return string HTML (already escaped) or ''.
 */
function spr_get_map_embed( $source, $title = '' ) {
	$url = is_numeric( $source ) ? get_post_meta( absint( $source ), '_spr_map_url', true ) : $source;
	$url = spr_sanitize_map_url( $url );
	if ( '' === $url ) {
		return '';
	}
	if ( '' === $title ) {
		$title = __( 'Location map', 'sp-realtors-core' );
	}
	return sprintf(
		'<iframe class="spr-map" src="%1$s" title="%2$s" width="600" height="400" style="border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>',
		esc_url( $url ),
		esc_attr( $title )
	);
}

/**
 * Load a template part: theme first (template-parts/{slug}.php), then plugin fallback.
 *
 * This lets the theme fully control markup while shortcodes/AJAX still work
 * with any theme.
 *
 * @param string $slug Template slug (letters, numbers, dashes).
 * @param array  $args Variables passed to the template as $args.
 * @return bool Whether a template was loaded.
 */
function spr_get_template_part( $slug, $args = array() ) {
	$slug = sanitize_key( $slug );
	if ( '' === $slug ) {
		return false;
	}

	$template = locate_template(
		array(
			'template-parts/' . $slug . '.php',
			'sp-realtors-core/' . $slug . '.php',
		)
	);

	if ( ! $template && file_exists( SPR_PATH . 'templates/' . $slug . '.php' ) ) {
		$template = SPR_PATH . 'templates/' . $slug . '.php';
	}

	$template = apply_filters( 'spr_template_path', $template, $slug, $args );

	if ( ! $template ) {
		return false;
	}

	load_template( $template, false, $args );
	return true;
}
