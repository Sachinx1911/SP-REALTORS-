<?php
/**
 * Term meta: location image (used by "Areas We Serve" cards).
 *
 * WHY HERE: The image belongs to the location term (content), not the theme.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the location image term meta.
 */
function spr_register_term_meta() {
	register_term_meta(
		'property_location',
		'spr_location_image',
		array(
			'type'              => 'integer',
			'single'            => true,
			'default'           => 0,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => function ( $allowed, $meta_key, $term_id ) {
				return current_user_can( 'edit_term', $term_id );
			},
		)
	);
}
add_action( 'init', 'spr_register_term_meta', 6 );

/**
 * Image picker markup.
 *
 * @param int $image_id Attachment ID.
 */
function spr_render_term_image_picker( $image_id ) {
	$image_id = absint( $image_id );
	?>
	<div class="spr-image-picker" data-spr-image-picker>
		<input type="hidden" name="spr_location_image" value="<?php echo esc_attr( $image_id ? $image_id : '' ); ?>" data-spr-image-input>
		<div class="spr-image-picker__preview" data-spr-image-preview>
			<?php
			if ( $image_id ) {
				echo wp_get_attachment_image( $image_id, 'thumbnail' );
			}
			?>
		</div>
		<button type="button" class="button" data-spr-image-select><?php esc_html_e( 'Choose image', 'sp-realtors-core' ); ?></button>
		<button type="button" class="button-link spr-image-picker__remove" data-spr-image-remove <?php echo $image_id ? '' : 'hidden'; ?>><?php esc_html_e( 'Remove', 'sp-realtors-core' ); ?></button>
	</div>
	<?php
	wp_nonce_field( 'spr_save_location_image', 'spr_location_image_nonce' );
}

/**
 * "Add new location" form field.
 */
function spr_location_add_form_field() {
	?>
	<div class="form-field term-image-wrap">
		<label><?php esc_html_e( 'Card image', 'sp-realtors-core' ); ?></label>
		<?php spr_render_term_image_picker( 0 ); ?>
		<p><?php esc_html_e( 'Shown on the "Areas We Serve" card on the homepage.', 'sp-realtors-core' ); ?></p>
	</div>
	<?php
}
add_action( 'property_location_add_form_fields', 'spr_location_add_form_field' );

/**
 * "Edit location" form field.
 *
 * @param WP_Term $term Term.
 */
function spr_location_edit_form_field( $term ) {
	?>
	<tr class="form-field term-image-wrap">
		<th scope="row"><label><?php esc_html_e( 'Card image', 'sp-realtors-core' ); ?></label></th>
		<td>
			<?php spr_render_term_image_picker( get_term_meta( $term->term_id, 'spr_location_image', true ) ); ?>
			<p class="description"><?php esc_html_e( 'Shown on the "Areas We Serve" card on the homepage.', 'sp-realtors-core' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'property_location_edit_form_fields', 'spr_location_edit_form_field' );

/**
 * Save location image.
 *
 * @param int $term_id Term ID.
 */
function spr_save_location_image( $term_id ) {
	if ( ! isset( $_POST['spr_location_image_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['spr_location_image_nonce'] ) ), 'spr_save_location_image' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	$image_id = isset( $_POST['spr_location_image'] ) ? absint( $_POST['spr_location_image'] ) : 0;

	if ( $image_id && wp_attachment_is_image( $image_id ) ) {
		update_term_meta( $term_id, 'spr_location_image', $image_id );
	} else {
		delete_term_meta( $term_id, 'spr_location_image' );
	}
}
add_action( 'created_property_location', 'spr_save_location_image' );
add_action( 'edited_property_location', 'spr_save_location_image' );

/**
 * Image column in the Locations list.
 *
 * @param array $columns Columns.
 * @return array
 */
function spr_location_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( 'name' === $key ) {
			$new['spr_image'] = __( 'Image', 'sp-realtors-core' );
		}
		$new[ $key ] = $label;
	}
	return $new;
}
add_filter( 'manage_edit-property_location_columns', 'spr_location_columns' );

/**
 * Render image column.
 *
 * @param string $content Content.
 * @param string $column  Column.
 * @param int    $term_id Term ID.
 * @return string
 */
function spr_location_column_content( $content, $column, $term_id ) {
	if ( 'spr_image' !== $column ) {
		return $content;
	}
	$image_id = absint( get_term_meta( $term_id, 'spr_location_image', true ) );
	return $image_id ? wp_get_attachment_image( $image_id, array( 48, 48 ) ) : '—';
}
add_filter( 'manage_property_location_custom_column', 'spr_location_column_content', 10, 3 );

/**
 * Location image ID helper for the theme.
 *
 * @param int|WP_Term $term Term.
 * @return int
 */
function spr_get_location_image_id( $term ) {
	$term_id = $term instanceof WP_Term ? $term->term_id : absint( $term );
	return absint( get_term_meta( $term_id, 'spr_location_image', true ) );
}
