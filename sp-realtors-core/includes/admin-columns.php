<?php
/**
 * Admin list-table columns, sorting, filters and the Featured ★ toggle.
 *
 * WHY HERE: Admin management UI for plugin-owned content.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Properties list.
 * ---------------------------------------------------------------------- */

/**
 * Property columns.
 *
 * @param array $columns Columns.
 * @return array
 */
function spr_property_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$new['spr_thumb'] = '<span class="screen-reader-text">' . esc_html__( 'Photo', 'sp-realtors-core' ) . '</span>';
		}
		if ( 'date' === $key ) {
			$new['spr_price']    = __( 'Price', 'sp-realtors-core' );
			$new['spr_status']   = __( 'Status', 'sp-realtors-core' );
			$new['spr_featured'] = '<span class="dashicons dashicons-star-filled" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html__( 'Featured', 'sp-realtors-core' ) . '</span>';
		}
		$new[ $key ] = $label;
	}
	return $new;
}
add_filter( 'manage_property_posts_columns', 'spr_property_columns' );

/**
 * Property column content.
 *
 * @param string $column  Column.
 * @param int    $post_id Post ID.
 */
function spr_property_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'spr_thumb':
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, array( 60, 60 ) );
			} else {
				echo '<span class="spr-no-thumb dashicons dashicons-format-image" aria-hidden="true"></span>';
			}
			break;

		case 'spr_price':
			$data = spr_get_property( $post_id );
			echo esc_html( $data ? $data['price_formatted'] : '—' );
			break;

		case 'spr_status':
			$statuses = spr_get_status_options();
			$status   = get_post_meta( $post_id, '_spr_status', true );
			echo isset( $statuses[ $status ] ) ? '<span class="spr-status spr-status--' . esc_attr( $status ) . '">' . esc_html( $statuses[ $status ] ) . '</span>' : '—';
			break;

		case 'spr_featured':
			$featured = (bool) get_post_meta( $post_id, '_spr_featured', true );
			if ( current_user_can( 'edit_post', $post_id ) ) {
				printf(
					'<button type="button" class="spr-star%1$s" data-spr-toggle-featured="%2$d" aria-pressed="%3$s" aria-label="%4$s"><span class="dashicons dashicons-star-%5$s" aria-hidden="true"></span></button>',
					$featured ? ' is-featured' : '',
					absint( $post_id ),
					$featured ? 'true' : 'false',
					esc_attr__( 'Toggle featured', 'sp-realtors-core' ),
					$featured ? 'filled' : 'empty'
				);
			} else {
				echo $featured ? '<span class="dashicons dashicons-star-filled" aria-hidden="true"></span>' : '';
			}
			break;
	}
}
add_action( 'manage_property_posts_custom_column', 'spr_property_column_content', 10, 2 );

/**
 * Sortable columns.
 *
 * @param array $columns Columns.
 * @return array
 */
function spr_property_sortable_columns( $columns ) {
	$columns['spr_price']    = 'spr_price';
	$columns['spr_featured'] = 'spr_featured';
	return $columns;
}
add_filter( 'manage_edit-property_sortable_columns', 'spr_property_sortable_columns' );

/**
 * Location / Purpose dropdown filters above the list.
 *
 * @param string $post_type Post type.
 */
function spr_property_list_filters( $post_type ) {
	if ( 'property' !== $post_type ) {
		return;
	}

	$filters = array(
		'spr_location' => array( 'property_location', __( 'All locations', 'sp-realtors-core' ) ),
		'spr_purpose'  => array( 'property_purpose', __( 'Buy & Rent', 'sp-realtors-core' ) ),
		'spr_ptype'    => array( 'property_type', __( 'All types', 'sp-realtors-core' ) ),
	);

	foreach ( $filters as $param => $filter ) {
		$selected = isset( $_GET[ $param ] ) ? sanitize_key( wp_unslash( $_GET[ $param ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list filter.
		$taxonomy = get_taxonomy( $filter[0] );
		?>
		<label class="screen-reader-text" for="<?php echo esc_attr( $param ); ?>"><?php echo esc_html( $taxonomy ? $taxonomy->labels->singular_name : $filter[1] ); ?></label>
		<?php
		wp_dropdown_categories(
			array(
				'taxonomy'        => $filter[0],
				'name'            => $param,
				'id'              => $param,
				'value_field'     => 'slug',
				'selected'        => $selected,
				'show_option_all' => $filter[1],
				'hide_empty'      => false,
				'hierarchical'    => true,
				'orderby'         => 'name',
			)
		);
	}
}
add_action( 'restrict_manage_posts', 'spr_property_list_filters' );

/**
 * Apply admin sort + dropdown filters.
 *
 * @param WP_Query $query Query.
 */
function spr_property_admin_query( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	global $pagenow;
	if ( 'edit.php' !== $pagenow || 'property' !== $query->get( 'post_type' ) ) {
		return;
	}

	$orderby = $query->get( 'orderby' );
	if ( 'spr_price' === $orderby || 'spr_featured' === $orderby ) {
		$key = 'spr_price' === $orderby ? '_spr_price' : '_spr_featured';
		// Named EXISTS / NOT EXISTS clauses keep properties without a value in the list.
		$query->set(
			'meta_query',
			array(
				'relation'   => 'OR',
				'spr_sort'   => array(
					'key'     => $key,
					'compare' => 'EXISTS',
					'type'    => 'NUMERIC',
				),
				'spr_nosort' => array(
					'key'     => $key,
					'compare' => 'NOT EXISTS',
				),
			)
		);
		$query->set( 'orderby', array( 'spr_sort' => $query->get( 'order' ) ? $query->get( 'order' ) : 'DESC' ) );
	}

	$tax_query = array();
	$map       = array(
		'spr_location' => 'property_location',
		'spr_purpose'  => 'property_purpose',
		'spr_ptype'    => 'property_type',
	);
	foreach ( $map as $param => $taxonomy ) {
		$slug = isset( $_GET[ $param ] ) ? sanitize_key( wp_unslash( $_GET[ $param ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list filter.
		if ( '' !== $slug && '0' !== $slug ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $slug,
			);
		}
	}
	if ( $tax_query ) {
		$query->set( 'tax_query', $tax_query ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}
}
add_action( 'pre_get_posts', 'spr_property_admin_query' );

/**
 * AJAX: toggle Featured from the list table.
 */
function spr_ajax_toggle_featured() {
	check_ajax_referer( 'spr_toggle_featured', 'nonce' );

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	if ( ! $post_id || 'property' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Not allowed.', 'sp-realtors-core' ) ), 403 );
	}

	$featured = ! (bool) get_post_meta( $post_id, '_spr_featured', true );
	if ( $featured ) {
		update_post_meta( $post_id, '_spr_featured', true );
	} else {
		delete_post_meta( $post_id, '_spr_featured' );
	}
	spr_flush_property_cache( $post_id );

	wp_send_json_success( array( 'featured' => $featured ) );
}
add_action( 'wp_ajax_spr_toggle_featured', 'spr_ajax_toggle_featured' );

/* -------------------------------------------------------------------------
 * Testimonials list.
 * ---------------------------------------------------------------------- */

/**
 * Testimonial columns.
 *
 * @param array $columns Columns.
 * @return array
 */
function spr_testimonial_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$new['spr_thumb'] = '<span class="screen-reader-text">' . esc_html__( 'Photo', 'sp-realtors-core' ) . '</span>';
		}
		if ( 'date' === $key ) {
			$new['spr_client'] = __( 'Detail', 'sp-realtors-core' );
			$new['spr_rating'] = __( 'Rating', 'sp-realtors-core' );
		}
		$new[ $key ] = $label;
	}
	return $new;
}
add_filter( 'manage_testimonial_posts_columns', 'spr_testimonial_columns' );

/**
 * Testimonial column content.
 *
 * @param string $column  Column.
 * @param int    $post_id Post ID.
 */
function spr_testimonial_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'spr_thumb':
			echo has_post_thumbnail( $post_id ) ? get_the_post_thumbnail( $post_id, array( 48, 48 ) ) : '—';
			break;
		case 'spr_client':
			$meta = get_post_meta( $post_id, '_spr_client_meta', true );
			echo esc_html( $meta ? $meta : '—' );
			break;
		case 'spr_rating':
			$rating = get_post_meta( $post_id, '_spr_rating', true );
			$rating = '' === $rating ? 5 : spr_sanitize_rating( $rating );
			/* translators: %d: rating out of 5 */
			echo '<span aria-label="' . esc_attr( sprintf( __( '%d out of 5', 'sp-realtors-core' ), $rating ) ) . '">' . esc_html( str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating ) ) . '</span>';
			break;
	}
}
add_action( 'manage_testimonial_posts_custom_column', 'spr_testimonial_column_content', 10, 2 );

/* -------------------------------------------------------------------------
 * Enquiries list.
 * ---------------------------------------------------------------------- */

/**
 * Enquiry columns.
 *
 * @param array $columns Columns.
 * @return array
 */
function spr_enquiry_columns( $columns ) {
	return array(
		'cb'           => isset( $columns['cb'] ) ? $columns['cb'] : '',
		'title'        => __( 'Enquiry', 'sp-realtors-core' ),
		'spr_phone'    => __( 'Phone', 'sp-realtors-core' ),
		'spr_email'    => __( 'Email', 'sp-realtors-core' ),
		'spr_property' => __( 'Property', 'sp-realtors-core' ),
		'spr_source'   => __( 'Form', 'sp-realtors-core' ),
		'date'         => __( 'Received', 'sp-realtors-core' ),
	);
}
add_filter( 'manage_spr_enquiry_posts_columns', 'spr_enquiry_columns' );

/**
 * Enquiry column content.
 *
 * @param string $column  Column.
 * @param int    $post_id Post ID.
 */
function spr_enquiry_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'spr_phone':
			$phone = get_post_meta( $post_id, '_spr_phone', true );
			$tel   = preg_replace( '/[^\d+]/', '', (string) $phone );
			echo $tel ? '<a href="' . esc_url( 'tel:' . $tel ) . '">' . esc_html( $phone ) . '</a>' : '—';
			break;
		case 'spr_email':
			$email = get_post_meta( $post_id, '_spr_email', true );
			echo is_email( $email ) ? '<a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a>' : '—';
			break;
		case 'spr_property':
			$pid = absint( get_post_meta( $post_id, '_spr_property_id', true ) );
			echo ( $pid && get_post( $pid ) ) ? '<a href="' . esc_url( get_permalink( $pid ) ) . '" target="_blank" rel="noopener">' . esc_html( get_the_title( $pid ) ) . '</a>' : '—';
			break;
		case 'spr_source':
			$source = get_post_meta( $post_id, '_spr_source', true );
			echo esc_html( 'property' === $source ? __( 'Property page', 'sp-realtors-core' ) : __( 'Contact page', 'sp-realtors-core' ) );
			break;
	}
}
add_action( 'manage_spr_enquiry_posts_custom_column', 'spr_enquiry_column_content', 10, 2 );

/**
 * Show the number of new (last 7 days) enquiries in the admin menu.
 */
function spr_enquiry_menu_badge() {
	global $submenu;
	if ( ! current_user_can( 'edit_others_posts' ) || empty( $submenu['edit.php?post_type=property'] ) ) {
		return;
	}

	$count = get_transient( 'spr_new_enquiry_count' );
	if ( false === $count ) {
		$recent = new WP_Query(
			array(
				'post_type'      => 'spr_enquiry',
				'post_status'    => 'private',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'date_query'     => array( array( 'after' => '7 days ago' ) ),
			)
		);
		$count = (int) $recent->found_posts;
		set_transient( 'spr_new_enquiry_count', $count, HOUR_IN_SECONDS );
	}

	if ( ! $count ) {
		return;
	}

	foreach ( $submenu['edit.php?post_type=property'] as $index => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=spr_enquiry' === $item[2] ) {
			$submenu['edit.php?post_type=property'][ $index ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . absint( $count ) . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
	}
}
add_action( 'admin_menu', 'spr_enquiry_menu_badge', 99 );
