<?php
/**
 * AJAX "Load More" for the property archive.
 *
 * WHY HERE: Data endpoint → plugin. The HTML for each card comes from the
 * theme's template-parts/card-property.php when available (via
 * spr_get_template_part), so the design still belongs to the theme.
 *
 * Without JavaScript the archive uses normal paginate_links() pagination.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Data the front-end script needs (theme passes this via wp_localize_script).
 *
 * @return array
 */
function spr_get_load_more_config() {
	$config = array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'action'  => 'spr_load_more',
		'nonce'   => wp_create_nonce( 'spr_load_more' ),
		'context' => array(),
	);

	if ( is_tax( spr_get_property_taxonomies() ) ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$config['context'] = array(
				'taxonomy' => $term->taxonomy,
				'term'     => $term->slug,
			);
		}
	}

	return $config;
}

/**
 * AJAX handler.
 */
function spr_ajax_load_more() {
	if ( ! check_ajax_referer( 'spr_load_more', 'nonce', false ) ) {
		// Usually a stale cached page: the script falls back to normal pagination.
		wp_send_json_error( array( 'code' => 'invalid_nonce' ), 403 );
	}

	$page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 2;
	$page = max( 2, min( $page, 500 ) );

	// Values are sanitized by spr_get_filter_values().
	$raw_filters = ( isset( $_POST['filters'] ) && is_array( $_POST['filters'] ) ) ? wp_unslash( $_POST['filters'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$filters     = spr_get_filter_values( $raw_filters );

	$args = array_merge(
		array(
			'post_type'           => 'property',
			'post_status'         => 'publish',
			'posts_per_page'      => spr_archive_per_page(),
			'paged'               => $page,
			'ignore_sticky_posts' => true,
		),
		spr_build_property_query_args( $filters )
	);

	// Taxonomy archive context (e.g. /location/kharghar/).
	$context_tax  = isset( $_POST['context_taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['context_taxonomy'] ) ) : '';
	$context_term = isset( $_POST['context_term'] ) ? sanitize_title( wp_unslash( $_POST['context_term'] ) ) : '';
	if ( $context_tax && $context_term && in_array( $context_tax, spr_get_property_taxonomies(), true ) ) {
		$context_clause = array(
			'taxonomy' => $context_tax,
			'field'    => 'slug',
			'terms'    => $context_term,
		);
		$args['tax_query'] = isset( $args['tax_query'] ) // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			? array( 'relation' => 'AND', $context_clause, $args['tax_query'] )
			: array( $context_clause );
	}

	$query = new WP_Query( apply_filters( 'spr_load_more_query_args', $args, $filters ) );

	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		spr_get_template_part(
			'card-property',
			array(
				'post_id' => get_the_ID(),
				'heading' => 'h2',
			)
		);
	}
	wp_reset_postdata();
	$html = ob_get_clean();

	wp_send_json_success(
		array(
			'html'      => $html,
			'page'      => $page,
			'max_pages' => (int) $query->max_num_pages,
			'has_more'  => $page < (int) $query->max_num_pages,
			'found'     => (int) $query->found_posts,
		)
	);
}
add_action( 'wp_ajax_spr_load_more', 'spr_ajax_load_more' );
add_action( 'wp_ajax_nopriv_spr_load_more', 'spr_ajax_load_more' );
