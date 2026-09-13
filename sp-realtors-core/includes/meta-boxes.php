<?php
/**
 * Property "Details" meta box (tabbed).
 *
 * WHY HERE: The editing UI for plugin-owned data belongs with the data.
 * Classic meta boxes work in both the block editor and the classic editor.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the meta box.
 */
function spr_add_property_meta_box() {
	add_meta_box(
		'spr_property_details',
		__( 'Property Details', 'sp-realtors-core' ),
		'spr_render_property_meta_box',
		'property',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_property', 'spr_add_property_meta_box' );

/**
 * Render the tabbed meta box.
 *
 * Without JavaScript every panel is visible (progressive enhancement).
 *
 * @param WP_Post $post Post.
 */
function spr_render_property_meta_box( $post ) {
	$groups = spr_get_property_field_groups();
	$fields = spr_get_property_fields();

	wp_nonce_field( 'spr_save_property_meta', 'spr_property_meta_nonce' );
	?>
	<div class="spr-metabox" data-spr-tabs>
		<div class="spr-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Property details sections', 'sp-realtors-core' ); ?>" hidden>
			<?php
			$first = true;
			foreach ( $groups as $group_key => $group_label ) :
				?>
				<button type="button" role="tab" class="spr-tab" id="spr-tab-<?php echo esc_attr( $group_key ); ?>" aria-controls="spr-panel-<?php echo esc_attr( $group_key ); ?>" aria-selected="<?php echo $first ? 'true' : 'false'; ?>" tabindex="<?php echo $first ? '0' : '-1'; ?>">
					<?php echo esc_html( $group_label ); ?>
				</button>
				<?php
				$first = false;
			endforeach;
			?>
		</div>

		<?php foreach ( $groups as $group_key => $group_label ) : ?>
			<div class="spr-panel" role="tabpanel" id="spr-panel-<?php echo esc_attr( $group_key ); ?>" aria-labelledby="spr-tab-<?php echo esc_attr( $group_key ); ?>">
				<h3 class="spr-panel__title"><?php echo esc_html( $group_label ); ?></h3>
				<div class="spr-fields">
					<?php
					foreach ( $fields as $key => $field ) {
						if ( $group_key === $field['group'] ) {
							spr_render_property_field( $post->ID, $key, $field );
						}
					}
					?>
				</div>
			</div>
		<?php endforeach; ?>

		<p class="spr-metabox__tip">
			<?php esc_html_e( 'Tip: choose Location, Type, Purpose (Buy/Rent) and Configuration (BHK) in the sidebar panels, and set the Main Photo as the featured image.', 'sp-realtors-core' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Render one field.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @param array  $field   Field config.
 */
function spr_render_property_field( $post_id, $key, $field ) {
	$value = get_post_meta( $post_id, $key, true );
	if ( '' === $value && ! metadata_exists( 'post', $post_id, $key ) ) {
		$value = $field['default'];
	}

	$id    = 'spr-field' . str_replace( '_', '-', $key );
	$name  = 'spr_meta[' . $key . ']';
	$desc  = isset( $field['description'] ) ? $field['description'] : '';
	$wide  = in_array( $field['input'], array( 'textarea', 'map', 'gallery', 'amenities' ), true );
	$class = 'spr-field spr-field--' . $field['input'] . ( $wide ? ' spr-field--wide' : '' );
	?>
	<div class="<?php echo esc_attr( $class ); ?>">
		<?php
		switch ( $field['input'] ) {
			case 'number':
				?>
				<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				<input type="number" min="0" step="1" inputmode="numeric" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ? absint( $value ) : '' ); ?>" class="widefat">
				<?php
				if ( '_spr_price' === $key ) :
					?>
					<span class="spr-price-preview" data-spr-price-preview aria-live="polite"></span>
					<?php
				endif;
				break;

			case 'text':
				?>
				<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="widefat">
				<?php
				break;

			case 'textarea':
				?>
				<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="4" class="widefat"><?php echo esc_textarea( $value ); ?></textarea>
				<?php
				break;

			case 'map':
				?>
				<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="3" class="widefat code" placeholder="https://www.google.com/maps/embed?pb=..."><?php echo esc_textarea( $value ); ?></textarea>
				<?php
				if ( $value ) {
					echo '<div class="spr-map-preview">' . spr_get_map_embed( $value ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside spr_get_map_embed().
				}
				break;

			case 'select':
				?>
				<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class="widefat">
					<?php foreach ( $field['choices'] as $choice_key => $choice_label ) : ?>
						<option value="<?php echo esc_attr( $choice_key ); ?>" <?php selected( (string) $value, (string) $choice_key ); ?>><?php echo esc_html( $choice_label ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php
				break;

			case 'checkbox':
				?>
				<label class="spr-checkbox" for="<?php echo esc_attr( $id ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( (bool) $value ); ?>>
					<strong><?php echo esc_html( $field['label'] ); ?></strong>
				</label>
				<?php
				break;

			case 'amenities':
				?>
				<fieldset>
					<legend><?php echo esc_html( $field['label'] ); ?></legend>
					<div class="spr-amenities">
						<?php foreach ( $field['choices'] as $choice_key => $choice_label ) : ?>
							<label class="spr-checkbox">
								<input type="checkbox" name="<?php echo esc_attr( $name ); ?>[]" value="<?php echo esc_attr( $choice_key ); ?>" <?php checked( in_array( $choice_key, (array) $value, true ) ); ?>>
								<?php echo esc_html( $choice_label ); ?>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
				<?php
				break;

			case 'gallery':
				?>
				<span class="spr-field__label"><?php echo esc_html( $field['label'] ); ?></span>
				<?php
				spr_render_gallery_field( $name, (array) $value );
				break;
		}

		if ( $desc ) :
			?>
			<p class="description"><?php echo esc_html( $desc ); ?></p>
			<?php
		endif;
		?>
	</div>
	<?php
}
