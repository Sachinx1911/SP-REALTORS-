<?php
/**
 * Fallback enquiry / contact form.
 *
 * Theme override: {theme}/template-parts/enquiry-form.php
 * Overrides MUST call spr_enquiry_hidden_fields( $args ) inside the <form>.
 *
 * @package SP_Realtors_Core
 *
 * @var array $args See spr_render_enquiry_form().
 */

defined( 'ABSPATH' ) || exit;

$spr_uid    = wp_unique_id( 'spr-enquiry-' );
$spr_status = isset( $args['status'] ) ? $args['status'] : array();
?>
<div class="spr-enquiry" id="spr-enquiry-<?php echo esc_attr( $args['source'] ); ?>">
	<?php if ( ! empty( $args['title'] ) ) : ?>
		<h2 class="spr-enquiry__title"><?php echo esc_html( $args['title'] ); ?></h2>
	<?php endif; ?>

	<?php if ( $spr_status ) : ?>
		<div class="spr-notice spr-notice--<?php echo esc_attr( $spr_status['type'] ); ?>" role="<?php echo 'success' === $spr_status['type'] ? 'status' : 'alert'; ?>" tabindex="-1">
			<?php echo esc_html( $spr_status['message'] ); ?>
		</div>
	<?php endif; ?>

	<form class="spr-enquiry__form" method="post" action="<?php echo esc_url( $args['action_url'] ); ?>">
		<?php spr_enquiry_hidden_fields( $args ); ?>

		<p class="spr-enquiry__field">
			<label for="<?php echo esc_attr( $spr_uid ); ?>-name"><?php esc_html_e( 'Your name', 'sp-realtors-core' ); ?> <span aria-hidden="true">*</span></label>
			<input type="text" id="<?php echo esc_attr( $spr_uid ); ?>-name" name="spr_name" required maxlength="100" autocomplete="name">
		</p>

		<p class="spr-enquiry__field">
			<label for="<?php echo esc_attr( $spr_uid ); ?>-phone"><?php esc_html_e( 'Phone number', 'sp-realtors-core' ); ?> <span aria-hidden="true">*</span></label>
			<input type="tel" id="<?php echo esc_attr( $spr_uid ); ?>-phone" name="spr_phone" required pattern="[0-9+\-\s\(\)]{10,20}" inputmode="tel" autocomplete="tel">
		</p>

		<?php if ( ! empty( $args['show_email'] ) ) : ?>
			<p class="spr-enquiry__field">
				<label for="<?php echo esc_attr( $spr_uid ); ?>-email"><?php esc_html_e( 'Email (optional)', 'sp-realtors-core' ); ?></label>
				<input type="email" id="<?php echo esc_attr( $spr_uid ); ?>-email" name="spr_email" autocomplete="email">
			</p>
		<?php endif; ?>

		<?php if ( ! empty( $args['show_message'] ) ) : ?>
			<p class="spr-enquiry__field">
				<label for="<?php echo esc_attr( $spr_uid ); ?>-message"><?php esc_html_e( 'Message (optional)', 'sp-realtors-core' ); ?></label>
				<textarea id="<?php echo esc_attr( $spr_uid ); ?>-message" name="spr_message" rows="4" maxlength="2000"></textarea>
			</p>
		<?php endif; ?>

		<p class="spr-enquiry__submit">
			<button type="submit" class="spr-btn spr-btn--primary"><?php echo esc_html( $args['button_label'] ); ?></button>
		</p>
	</form>
</div>
