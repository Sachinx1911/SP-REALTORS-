<?php
/**
 * Admin assets + Media Library gallery field.
 *
 * WHY HERE: The gallery stores attachment IDs in property meta (plugin data).
 * Front-end rendering (with srcset) is done by the theme using core image functions.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue admin CSS/JS only on the screens that need them.
 *
 * @param string $hook_suffix Current admin page.
 */
function spr_admin_enqueue_assets( $hook_suffix ) {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$is_property_edit = in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) && 'property' === $screen->post_type;
	$is_property_list = 'edit.php' === $hook_suffix && in_array( $screen->post_type, array( 'property', 'testimonial', 'spr_enquiry' ), true );
	$is_location_term = in_array( $hook_suffix, array( 'edit-tags.php', 'term.php' ), true ) && 'property_location' === $screen->taxonomy;
	$is_enquiry_view  = 'post.php' === $hook_suffix && 'spr_enquiry' === $screen->post_type;

	if ( ! $is_property_edit && ! $is_property_list && ! $is_location_term && ! $is_enquiry_view ) {
		return;
	}

	$version = SPR_VERSION . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.' . filemtime( SPR_PATH . 'assets/admin/admin.js' ) : '' );

	wp_enqueue_style( 'spr-admin', SPR_URL . 'assets/admin/admin.css', array(), $version );

	$deps = array();
	if ( $is_property_edit || $is_location_term ) {
		wp_enqueue_media();
		$deps = array( 'jquery', 'jquery-ui-sortable', 'media-editor' );
	}

	wp_enqueue_script( 'spr-admin', SPR_URL . 'assets/admin/admin.js', $deps, $version, true );

	wp_localize_script(
		'spr-admin',
		'sprAdmin',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'toggleNonce' => wp_create_nonce( 'spr_toggle_featured' ),
			'i18n'        => array(
				'galleryTitle'  => __( 'Select gallery images', 'sp-realtors-core' ),
				'galleryButton' => __( 'Add to gallery', 'sp-realtors-core' ),
				'imageTitle'    => __( 'Select image', 'sp-realtors-core' ),
				'imageButton'   => __( 'Use this image', 'sp-realtors-core' ),
				'remove'        => __( 'Remove image', 'sp-realtors-core' ),
				'moveLeft'      => __( 'Move left', 'sp-realtors-core' ),
				'moveRight'     => __( 'Move right', 'sp-realtors-core' ),
				'crore'         => __( 'Cr', 'sp-realtors-core' ),
				'lakh'          => __( 'L', 'sp-realtors-core' ),
				'preview'       => __( 'Shows as:', 'sp-realtors-core' ),
				'error'         => __( 'Could not update. Please reload the page.', 'sp-realtors-core' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'spr_admin_enqueue_assets' );

/**
 * Gallery field markup.
 *
 * @param string $name Input name.
 * @param int[]  $ids  Attachment IDs.
 */
function spr_render_gallery_field( $name, $ids ) {
	$ids = spr_sanitize_id_list( $ids );
	?>
	<div class="spr-gallery" data-spr-gallery>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>" data-spr-gallery-input>
		<ul class="spr-gallery__list" data-spr-gallery-list>
			<?php foreach ( $ids as $id ) : ?>
				<?php spr_render_gallery_item( $id ); ?>
			<?php endforeach; ?>
		</ul>
		<button type="button" class="button button-secondary" data-spr-gallery-add>
			<span class="dashicons dashicons-format-gallery" aria-hidden="true"></span>
			<?php esc_html_e( 'Add images', 'sp-realtors-core' ); ?>
		</button>
	</div>
	<?php
}

/**
 * One gallery thumbnail (also used as a JS template via data attributes).
 *
 * @param int $id Attachment ID.
 */
function spr_render_gallery_item( $id ) {
	?>
	<li class="spr-gallery__item" data-id="<?php echo esc_attr( $id ); ?>">
		<?php echo wp_get_attachment_image( $id, 'thumbnail', false, array( 'draggable' => 'false' ) ); ?>
		<div class="spr-gallery__actions">
			<button type="button" class="spr-gallery__btn" data-spr-move="-1" aria-label="<?php esc_attr_e( 'Move left', 'sp-realtors-core' ); ?>"><span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span></button>
			<button type="button" class="spr-gallery__btn" data-spr-move="1" aria-label="<?php esc_attr_e( 'Move right', 'sp-realtors-core' ); ?>"><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></button>
			<button type="button" class="spr-gallery__btn spr-gallery__btn--remove" data-spr-remove aria-label="<?php esc_attr_e( 'Remove image', 'sp-realtors-core' ); ?>"><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>
		</div>
	</li>
	<?php
}
