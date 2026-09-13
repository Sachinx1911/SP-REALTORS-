<?php
/**
 * Property archive filtering, sorting and search integration.
 *
 * WHY HERE: Filtering is data logic. Keeping it in the plugin means
 * /properties/?location=kharghar&purpose=buy works with ANY theme, and the
 * same query builder powers the archive, AJAX Load More and shortcodes.
 *
 * Supported query vars (string, comma list, or array for checkboxes):
 *   location[]  ptype[]  purpose[]  config[]  budget  sort
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Filter key => taxonomy map.
 *
 * @return array
 */
function spr_get_filter_taxonomy_map() {
	return apply_filters(
		'spr_filter_taxonomy_map',
		array(
			'location' => 'property_location',
			'ptype'    => 'property_type',
			'purpose'  => 'property_purpose',
			'config'   => 'property_configuration',
		)
	);
}

/**
 * Sort options.
 *
 * @return array key => label
 */
function spr_get_sort_options() {
	return apply_filters(
		'spr_sort_options',
		array(
			'latest'     => __( 'Newest first', 'sp-realtors-core' ),
			'price_asc'  => __( 'Price: Low to High', 'sp-realtors-core' ),
			'price_desc' => __( 'Price: High to Low', 'sp-realtors-core' ),
			'area_desc'  => __( 'Area: Largest first', 'sp-realtors-core' ),
		)
	);
}

/**
 * Properties per archive page.
 *
 * @return int
 */
function spr_archive_per_page() {
	return max( 1, absint( apply_filters( 'spr_archive_per_page', 9 ) ) );
}

/**
 * Register public query vars.
 *
 * @param array $vars Query vars.
 * @return array
 */
function spr_register_query_vars( $vars ) {
	$keys = array_merge( array_keys( spr_get_filter_taxonomy_map() ), array( 'budget', 'sort' ) );
	return array_merge( $vars, $keys );
}
add_filter( 'query_vars', 'spr_register_query_vars' );

/**
 * Normalize a slug list from string / comma list / array.
 *
 * @param mixed $value Raw.
 * @return string[]
 */
function spr_normalize_slug_list( $value ) {
	if ( is_string( $value ) ) {
		$value = explode( ',', $value );
	}
	if ( ! is_array( $value ) ) {
		return array();
	}

	$slugs = array();
	foreach ( $value as $item ) {
		if ( ! is_scalar( $item ) ) {
			continue;
		}
		$slug = sanitize_title( (string) $item );
		if ( '' !== $slug && ! in_array( $slug, $slugs, true ) ) {
			$slugs[] = $slug;
		}
		if ( count( $slugs ) >= 20 ) {
			break;
		}
	}
	return $slugs;
}

/**
 * Sanitized filter values.
 *
 * @param array|null $source Raw array (e.g. AJAX POST). Null = read the main query vars.
 * @param bool       $include_queried_term On a taxonomy archive, include the current term (for checked state).
 * @return array
 */
function spr_get_filter_values( $source = null, $include_queried_term = true ) {
	$values = array();

	foreach ( spr_get_filter_taxonomy_map() as $key => $taxonomy ) {
		$raw            = is_array( $source ) ? ( isset( $source[ $key ] ) ? $source[ $key ] : array() ) : get_query_var( $key );
		$values[ $key ] = spr_normalize_slug_list( $raw );

		if ( null === $source && $include_queried_term && is_tax( $taxonomy ) ) {
			$term = get_queried_object();
			if ( $term instanceof WP_Term && ! in_array( $term->slug, $values[ $key ], true ) ) {
				$values[ $key ][] = $term->slug;
			}
		}
	}

	$budget = is_array( $source ) ? ( isset( $source['budget'] ) ? $source['budget'] : '' ) : get_query_var( 'budget' );
	$budget = is_scalar( $budget ) ? sanitize_key( (string) $budget ) : '';
	$ranges = spr_get_budget_ranges();

	$sort    = is_array( $source ) ? ( isset( $source['sort'] ) ? $source['sort'] : '' ) : get_query_var( 'sort' );
	$sort    = is_scalar( $sort ) ? sanitize_key( (string) $sort ) : '';
	$sorting = spr_get_sort_options();

	$values['budget'] = isset( $ranges[ $budget ] ) ? $budget : '';
	$values['sort']   = isset( $sorting[ $sort ] ) ? $sort : 'latest';

	return apply_filters( 'spr_filter_values', $values, $source );
}

/**
 * Whether any filter (other than sort) is active.
 *
 * @param array $filters Filter values.
 * @return bool
 */
function spr_has_active_filters( $filters ) {
	foreach ( array_keys( spr_get_filter_taxonomy_map() ) as $key ) {
		if ( ! empty( $filters[ $key ] ) ) {
			return true;
		}
	}
	return ! empty( $filters['budget'] );
}

/**
 * Build WP_Query args (tax_query, meta_query, orderby) from filter values.
 *
 * @param array $filters Sanitized values from spr_get_filter_values().
 * @return array
 */
function spr_build_property_query_args( $filters ) {
	$args      = array();
	$tax_query = array();

	foreach ( spr_get_filter_taxonomy_map() as $key => $taxonomy ) {
		if ( ! empty( $filters[ $key ] ) ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $filters[ $key ],
				'operator' => 'IN',
			);
		}
	}

	if ( $tax_query ) {
		$tax_query['relation'] = 'AND';
		$args['tax_query']     = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}

	$meta_query = array();
	$ranges     = spr_get_budget_ranges();

	if ( ! empty( $filters['budget'] ) && isset( $ranges[ $filters['budget'] ] ) ) {
		$range = $ranges[ $filters['budget'] ];
		if ( ! empty( $range['min'] ) ) {
			$meta_query[] = array(
				'key'     => '_spr_price',
				'value'   => absint( $range['min'] ),
				'compare' => '>=',
				'type'    => 'UNSIGNED',
			);
		}
		if ( ! empty( $range['max'] ) ) {
			$meta_query[] = array(
				'key'     => '_spr_price',
				'value'   => absint( $range['max'] ),
				'compare' => '<',
				'type'    => 'UNSIGNED',
			);
		}
	}

	$sort = isset( $filters['sort'] ) ? $filters['sort'] : 'latest';

	switch ( $sort ) {
		case 'price_asc':
		case 'price_desc':
			// Listings without a price ("Price on Request") are left out of price sorting.
			$meta_query['spr_price_clause'] = array(
				'key'     => '_spr_price',
				'compare' => 'EXISTS',
				'type'    => 'UNSIGNED',
			);
			$args['orderby']                = array(
				'spr_price_clause' => 'price_asc' === $sort ? 'ASC' : 'DESC',
				'date'             => 'DESC',
			);
			break;

		case 'area_desc':
			$meta_query['spr_area_clause'] = array(
				'key'     => '_spr_area',
				'compare' => 'EXISTS',
				'type'    => 'UNSIGNED',
			);
			$args['orderby']               = array(
				'spr_area_clause' => 'DESC',
				'date'            => 'DESC',
			);
			break;

		default:
			$args['orderby'] = array( 'date' => 'DESC' );
	}

	if ( $meta_query ) {
		$meta_query['relation'] = 'AND';
		$args['meta_query']     = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
	}

	return apply_filters( 'spr_property_query_args', $args, $filters );
}

/**
 * Apply filters to the main property archive / property taxonomy archives.
 *
 * @param WP_Query $query Query.
 */
function spr_filter_property_archive( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_search() && ! $query->get( 'post_type' ) ) {
		$query->set( 'post_type', apply_filters( 'spr_search_post_types', array( 'post', 'page', 'property' ) ) );
		return;
	}

	if ( ! $query->is_post_type_archive( 'property' ) && ! $query->is_tax( spr_get_property_taxonomies() ) ) {
		return;
	}

	$filters = spr_get_filter_values( null, false );
	$args    = spr_build_property_query_args( $filters );

	foreach ( $args as $key => $value ) {
		if ( 'tax_query' === $key ) {
			// Keep the taxonomy archive's own term restriction and add ours.
			$existing = $query->get( 'tax_query' );
			$value    = is_array( $existing ) && $existing ? array_merge( array( 'relation' => 'AND' ), array( $existing ), array( $value ) ) : $value;
		}
		$query->set( $key, $value );
	}

	$query->set( 'post_type', 'property' );
	$query->set( 'posts_per_page', spr_archive_per_page() );
	$query->set( 'ignore_sticky_posts', true );
}
add_action( 'pre_get_posts', 'spr_filter_property_archive' );

/**
 * Archive URL for properties.
 *
 * @return string
 */
function spr_get_properties_url() {
	$url = get_post_type_archive_link( 'property' );
	return $url ? $url : home_url( '/properties/' );
}

/**
 * Build an archive URL with filters (for requirement cards, location cards, etc.).
 *
 * @param array $filters e.g. [ 'purpose' => 'buy', 'location' => [ 'kharghar' ] ].
 * @return string Raw URL (escape on output).
 */
function spr_get_filtered_properties_url( $filters = array() ) {
	$allowed = array_merge( array_keys( spr_get_filter_taxonomy_map() ), array( 'budget', 'sort' ) );
	$args    = array();

	foreach ( $filters as $key => $value ) {
		if ( ! in_array( $key, $allowed, true ) ) {
			continue;
		}
		if ( in_array( $key, array( 'budget', 'sort' ), true ) ) {
			$value = sanitize_key( (string) $value );
			if ( '' !== $value ) {
				$args[ $key ] = $value;
			}
			continue;
		}
		$slugs = spr_normalize_slug_list( $value );
		if ( 1 === count( $slugs ) ) {
			$args[ $key ] = $slugs[0];
		} elseif ( $slugs ) {
			$args[ $key ] = $slugs;
		}
	}

	return $args ? add_query_arg( $args, spr_get_properties_url() ) : spr_get_properties_url();
}

/**
 * Terms for a filter list (non-empty by default).
 *
 * @param string $key        Filter key (location, ptype, purpose, config).
 * @param bool   $hide_empty Hide terms without published properties.
 * @return WP_Term[]
 */
function spr_get_filter_terms( $key, $hide_empty = false ) {
	$map = spr_get_filter_taxonomy_map();
	if ( ! isset( $map[ $key ] ) ) {
		return array();
	}
	$terms = get_terms(
		array(
			'taxonomy'   => $map[ $key ],
			'hide_empty' => $hide_empty,
			'orderby'    => 'purpose' === $key || 'config' === $key ? 'term_id' : 'name',
		)
	);
	return is_wp_error( $terms ) ? array() : $terms;
}
