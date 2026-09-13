<?php
/**
 * Custom Post Type: testimonial (+ its meta box).
 *
 * WHY HERE: Client reviews are content the broker owns; they must survive a
 * theme change. Not publicly queryable — they are shown inside pages only.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the `testimonial` post type.
 */
function spr_register_testimonial_post_type() {
	$labels = array(
		'name'               => _x( 'Testimonials', 'post type general name', 'sp-realtors-core' ),
		'singular_name'      => _x( 'Testimonial', 'post type singular name', 'sp-realtors-core' ),
		'menu_name'          => _x( 'Testimonials', 'admin menu', 'sp-realtors-core' ),
		'add_new'            => __( 'Add New', 'sp-realtors-core' ),
		'add_new_item'       => __( 'Add New Testimonial', 'sp-realtors-core' ),
		'edit_item'          => __( 'Edit Testimonial', 'sp-realtors-core' ),
		'new_item'           => __( 'New Testimonial', 'sp-realtors-core' ),
		'all_items'          => __( 'All Testimonials', 'sp-realtors-core' ),
		'search_items'       => __( 'Search Testimonials', 'sp-realtors-core' ),
		'not_found'          => __( 'No testimonials found.', 'sp-realtors-core' ),
		'not_found_in_trash' => __( 'No testimonials found in Trash.', 'sp-realtors-core' ),
		'featured_image'     => __( 'Client Photo', 'sp-realtors-core' ),
		'set_featured_image' => __( 'Set client photo', 'sp-realtors-core' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_rest'        => true,
		'menu_position'       => 6,
		'menu_icon'           => 'dashicons-format-quote',
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes' ),
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => false,
	);

	register_post_type( 'testimonial', apply_filters( 'spr_testimonial_post_type_args', $args ) );

	$auth = function ( $allowed, $meta_key, $object_id ) {
		return current_user_can( 'edit_post', $object_id );
	};

	register_post_meta(
		'testimonial',
		'_spr_rating',
		array(
			'type'              => 'integer',
			'single'            => true,
			'default'           => 5,
			'show_in_rest'      => true,
			'sanitize_callback' => 'spr_sanitize_rating',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'testimonial',
		'_spr_client_meta',
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => '',
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth,
		)
	);
}
add_action( 'init', 'spr_register_testimonial_post_type' );

/**
 * Clamp a rating to 1–5.
 *
 * @param mixed $value Raw.
 * @return int
 */
function spr_sanitize_rating( $value ) {
	return min( 5, max( 1, absint( $value ) ) );
}

/**
 * Title placeholder.
 *
 * @param string  $text Placeholder.
 * @param WP_Post $post Post.
 * @return string
 */
function spr_testimonial_title_placeholder( $text, $post ) {
	return 'testimonial' === $post->post_type ? __( 'Client name', 'sp-realtors-core' ) : $text;
}
add_filter( 'enter_title_here', 'spr_testimonial_title_placeholder', 10, 2 );

/**
 * Register the testimonial details meta box.
 */
function spr_testimonial_meta_box() {
	add_meta_box(
		'spr_testimonial_details',
		__( 'Testimonial Details', 'sp-realtors-core' ),
		'spr_render_testimonial_meta_box',
		'testimonial',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'spr_testimonial_meta_box' );

/**
 * Render testimonial meta box.
 *
 * @param WP_Post $post Post.
 */
function spr_render_testimonial_meta_box( $post ) {
	$rating = get_post_meta( $post->ID, '_spr_rating', true );
	$rating = '' === $rating ? 5 : spr_sanitize_rating( $rating );
	$meta   = get_post_meta( $post->ID, '_spr_client_meta', true );

	wp_nonce_field( 'spr_save_testimonial', 'spr_testimonial_nonce' );
	?>
	<p>
		<label for="spr_client_meta"><strong><?php esc_html_e( 'Client detail', 'sp-realtors-core' ); ?></strong></label><br>
		<input type="text" id="spr_client_meta" name="spr_client_meta" class="widefat" value="<?php echo esc_attr( $meta ); ?>" placeholder="<?php esc_attr_e( 'e.g. Bought 2 BHK in Kharghar', 'sp-realtors-core' ); ?>">
	</p>
	<p>
		<label for="spr_rating"><strong><?php esc_html_e( 'Rating', 'sp-realtors-core' ); ?></strong></label><br>
		<select id="spr_rating" name="spr_rating">
			<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
				<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $rating, $i ); ?>>
					<?php
					/* translators: %d: number of stars */
					echo esc_html( sprintf( _n( '%d star', '%d stars', $i, 'sp-realtors-core' ), $i ) );
					?>
				</option>
			<?php endfor; ?>
		</select>
	</p>
	<p class="description"><?php esc_html_e( 'Write the review in the main editor. Set the client photo as the featured image (optional).', 'sp-realtors-core' ); ?></p>
	<?php
}

/**
 * Save testimonial meta.
 *
 * @param int $post_id Post ID.
 */
function spr_save_testimonial_meta( $post_id ) {
	if ( ! isset( $_POST['spr_testimonial_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['spr_testimonial_nonce'] ) ), 'spr_save_testimonial' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['spr_rating'] ) ) {
		update_post_meta( $post_id, '_spr_rating', spr_sanitize_rating( wp_unslash( $_POST['spr_rating'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized by spr_sanitize_rating().
	}
	if ( isset( $_POST['spr_client_meta'] ) ) {
		update_post_meta( $post_id, '_spr_client_meta', sanitize_text_field( wp_unslash( $_POST['spr_client_meta'] ) ) );
	}
}
add_action( 'save_post_testimonial', 'spr_save_testimonial_meta' );

/**
 * Query testimonials for display.
 *
 * @param int $count Number to show.
 * @return WP_Query
 */
function spr_testimonials_query( $count = 6 ) {
	return new WP_Query(
		array(
			'post_type'      => 'testimonial',
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, absint( $count ) ),
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'no_found_rows'  => true,
		)
	);
}
