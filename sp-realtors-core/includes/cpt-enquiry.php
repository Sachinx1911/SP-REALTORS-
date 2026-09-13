<?php
/**
 * Private CPT: spr_enquiry (leads from contact/enquiry forms).
 *
 * WHY HERE: Leads are business data. Saving them in the database (not only
 * emailing them) means no enquiry is lost if mail delivery fails. Only
 * Editors/Administrators can view them; nobody can create them from wp-admin.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the `spr_enquiry` post type.
 */
function spr_register_enquiry_post_type() {
	$labels = array(
		'name'               => _x( 'Enquiries', 'post type general name', 'sp-realtors-core' ),
		'singular_name'      => _x( 'Enquiry', 'post type singular name', 'sp-realtors-core' ),
		'menu_name'          => _x( 'Enquiries', 'admin menu', 'sp-realtors-core' ),
		'all_items'          => __( 'Enquiries', 'sp-realtors-core' ),
		'edit_item'          => __( 'Enquiry', 'sp-realtors-core' ),
		'search_items'       => __( 'Search Enquiries', 'sp-realtors-core' ),
		'not_found'          => __( 'No enquiries yet.', 'sp-realtors-core' ),
		'not_found_in_trash' => __( 'No enquiries found in Trash.', 'sp-realtors-core' ),
	);

	register_post_type(
		'spr_enquiry',
		array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=property',
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'show_in_rest'        => false,
			'supports'            => array( 'title' ),
			'rewrite'             => false,
			'query_var'           => false,
			'map_meta_cap'        => true,
			'capability_type'     => 'post',
			// Only users who can edit others' posts (Editors, Admins) may see leads. Nobody can add one manually.
			'capabilities'        => array(
				'create_posts'           => 'do_not_allow',
				'edit_posts'             => 'edit_others_posts',
				'edit_others_posts'      => 'edit_others_posts',
				'edit_published_posts'   => 'edit_others_posts',
				'edit_private_posts'     => 'edit_others_posts',
				'publish_posts'          => 'edit_others_posts',
				'read_private_posts'     => 'edit_others_posts',
				'delete_posts'           => 'delete_others_posts',
				'delete_private_posts'   => 'delete_others_posts',
				'delete_published_posts' => 'delete_others_posts',
				'delete_others_posts'    => 'delete_others_posts',
			),
		)
	);
}
add_action( 'init', 'spr_register_enquiry_post_type' );

/**
 * Enquiry fields: meta key => label.
 *
 * @return array
 */
function spr_get_enquiry_fields() {
	return array(
		'_spr_name'        => __( 'Name', 'sp-realtors-core' ),
		'_spr_phone'       => __( 'Phone', 'sp-realtors-core' ),
		'_spr_email'       => __( 'Email', 'sp-realtors-core' ),
		'_spr_message'     => __( 'Message', 'sp-realtors-core' ),
		'_spr_property_id' => __( 'Property', 'sp-realtors-core' ),
		'_spr_source'      => __( 'Source', 'sp-realtors-core' ),
		'_spr_page_url'    => __( 'Sent from page', 'sp-realtors-core' ),
	);
}

/**
 * Store an enquiry. Data MUST already be sanitized by the caller.
 *
 * @param array $data Keys: name, phone, email, message, property_id, source, page_url.
 * @return int|WP_Error Post ID.
 */
function spr_create_enquiry( $data ) {
	$data = wp_parse_args(
		$data,
		array(
			'name'        => '',
			'phone'       => '',
			'email'       => '',
			'message'     => '',
			'property_id' => 0,
			'source'      => 'contact',
			'page_url'    => '',
		)
	);

	$title = $data['name'];
	if ( $data['property_id'] ) {
		/* translators: 1: client name, 2: property title */
		$title = sprintf( __( '%1$s — %2$s', 'sp-realtors-core' ), $data['name'], spr_plain_text( get_the_title( $data['property_id'] ) ) );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'spr_enquiry',
			'post_status' => 'private',
			'post_title'  => sanitize_text_field( $title ),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	update_post_meta( $post_id, '_spr_name', sanitize_text_field( $data['name'] ) );
	update_post_meta( $post_id, '_spr_phone', sanitize_text_field( $data['phone'] ) );
	update_post_meta( $post_id, '_spr_email', sanitize_email( $data['email'] ) );
	update_post_meta( $post_id, '_spr_message', sanitize_textarea_field( $data['message'] ) );
	update_post_meta( $post_id, '_spr_property_id', absint( $data['property_id'] ) );
	update_post_meta( $post_id, '_spr_source', sanitize_key( $data['source'] ) );
	update_post_meta( $post_id, '_spr_page_url', esc_url_raw( $data['page_url'] ) );

	delete_transient( 'spr_new_enquiry_count' );

	return $post_id;
}

/**
 * Read-only details meta box.
 */
function spr_enquiry_meta_box() {
	add_meta_box( 'spr_enquiry_details', __( 'Enquiry Details', 'sp-realtors-core' ), 'spr_render_enquiry_meta_box', 'spr_enquiry', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'spr_enquiry_meta_box' );

/**
 * Render enquiry details.
 *
 * @param WP_Post $post Post.
 */
function spr_render_enquiry_meta_box( $post ) {
	echo '<table class="form-table spr-enquiry-table" role="presentation"><tbody>';

	foreach ( spr_get_enquiry_fields() as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>';

		switch ( $key ) {
			case '_spr_phone':
				$tel = preg_replace( '/[^\d+]/', '', (string) $value );
				echo $tel ? '<a href="' . esc_url( 'tel:' . $tel ) . '">' . esc_html( $value ) . '</a>' : '—';
				break;
			case '_spr_email':
				echo is_email( $value ) ? '<a href="' . esc_url( 'mailto:' . $value ) . '">' . esc_html( $value ) . '</a>' : '—';
				break;
			case '_spr_message':
				echo $value ? wp_kses_post( wpautop( esc_html( $value ) ) ) : '—';
				break;
			case '_spr_property_id':
				$pid = absint( $value );
				if ( $pid && get_post( $pid ) ) {
					printf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $pid ) ), esc_html( get_the_title( $pid ) ) );
				} else {
					echo '—';
				}
				break;
			case '_spr_page_url':
				echo $value ? '<a href="' . esc_url( $value ) . '" target="_blank" rel="noopener">' . esc_html( $value ) . '</a>' : '—';
				break;
			default:
				echo '' !== (string) $value ? esc_html( $value ) : '—';
		}

		echo '</td></tr>';
	}

	echo '<tr><th scope="row">' . esc_html__( 'Received', 'sp-realtors-core' ) . '</th><td>' . esc_html( get_the_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $post ) ) . '</td></tr>';
	echo '</tbody></table>';
}

/**
 * Remove the "Add New" UI and the title editor for enquiries (they are read-only records).
 */
function spr_enquiry_remove_editor_bits() {
	remove_post_type_support( 'spr_enquiry', 'title' );
	remove_meta_box( 'submitdiv', 'spr_enquiry', 'side' );
}
add_action( 'add_meta_boxes_spr_enquiry', 'spr_enquiry_remove_editor_bits' );

/**
 * Hide row "Edit"/"Quick Edit" actions; keep View details + Trash.
 *
 * @param array   $actions Actions.
 * @param WP_Post $post    Post.
 * @return array
 */
function spr_enquiry_row_actions( $actions, $post ) {
	if ( 'spr_enquiry' !== $post->post_type ) {
		return $actions;
	}
	unset( $actions['inline hide-if-no-js'] );
	if ( isset( $actions['edit'] ) ) {
		$actions['edit'] = sprintf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $post->ID ) ), esc_html__( 'View details', 'sp-realtors-core' ) );
	}
	return $actions;
}
add_filter( 'post_row_actions', 'spr_enquiry_row_actions', 10, 2 );
