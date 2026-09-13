<?php
/**
 * Save property meta from the "Property Details" meta box.
 *
 * WHY HERE: Data persistence is functionality → plugin territory.
 * Order of checks: nonce → autosave/revision → capability → sanitize per field.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Save handler.
 *
 * @param int $post_id Post ID.
 */
function spr_save_property_meta( $post_id ) {
	if ( ! isset( $_POST['spr_property_meta_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['spr_property_meta_nonce'] ) ), 'spr_save_property_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Each value is sanitized individually below by spr_sanitize_property_meta().
	$raw = ( isset( $_POST['spr_meta'] ) && is_array( $_POST['spr_meta'] ) ) ? wp_unslash( $_POST['spr_meta'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	foreach ( spr_get_property_fields() as $key => $field ) {
		$present = array_key_exists( $key, $raw );

		// Unchecked checkboxes are not submitted at all: treat as "off".
		if ( ! $present && in_array( $field['input'], array( 'checkbox', 'amenities' ), true ) ) {
			delete_post_meta( $post_id, $key );
			continue;
		}
		if ( ! $present ) {
			continue;
		}

		$value = spr_sanitize_property_meta( $raw[ $key ], $key );

		if ( '' === $value || 0 === $value || false === $value || array() === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	// Convenience: if no Main Photo is set, use the first gallery image.
	if ( ! has_post_thumbnail( $post_id ) ) {
		$gallery = (array) get_post_meta( $post_id, '_spr_gallery', true );
		if ( ! empty( $gallery[0] ) ) {
			set_post_thumbnail( $post_id, absint( $gallery[0] ) );
		}
	}

	spr_flush_property_cache( $post_id );

	/**
	 * Fires after property meta is saved from the meta box.
	 *
	 * @param int $post_id Property ID.
	 */
	do_action( 'spr_property_meta_saved', $post_id );
}
add_action( 'save_post_property', 'spr_save_property_meta' );
