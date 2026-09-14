<?php
/**
 * Enquiry / contact form override.
 *
 * Two looks, driven by $args['source'] (set by spr_render_enquiry_form()):
 *   - 'property': green "Send on WhatsApp" submit + a separate "Call Now" tel: link
 *   - 'contact':  a single "Send Message" submit
 *
 * MUST call spr_enquiry_hidden_fields( $args ) inside the <form> (nonce + honeypot).
 *
 * @package SP_Realtors
 *
 * @var array $args See spr_render_enquiry_form() in the plugin.
 */

defined( 'ABSPATH' ) || exit;

$sp_uid      = wp_unique_id( 'spr-enquiry-' );
$sp_status   = isset( $args['status'] ) ? $args['status'] : array();
$sp_property = 'property' === $args['source'];
$sp_tel      = sp_realtors_core_active() ? spr_tel_url() : '';
?>
<div class="spr-enquiry" id="spr-enquiry-<?php echo esc_attr( $args['source'] ); ?>">
	<?php if ( ! empty( $args['title'] ) ) : ?>
		<h2 class="spr-enquiry__title"><?php echo esc_html( $args['title'] ); ?></h2>
	<?php endif; ?>

	<?php if ( $sp_status ) : ?>
		<div class="spr-notice spr-notice--<?php echo esc_attr( $sp_status['type'] ); ?>" role="<?php echo 'success' === $sp_status['type'] ? 'status' : 'alert'; ?>" tabindex="-1">
			<?php echo esc_html( $sp_status['message'] ); ?>
		</div>
	<?php endif; ?>

	<form class="spr-enquiry__form" method="post" action="<?php echo esc_url( $args['action_url'] ); ?>">
		<?php spr_enquiry_hidden_fields( $args ); ?>

		<p class="spr-enquiry__field">
			<label for="<?php echo esc_attr( $sp_uid ); ?>-name"><?php esc_html_e( 'Your Name', 'sp-realtors' ); ?> <span aria-hidden="true">*</span></label>
			<input type="text" id="<?php echo esc_attr( $sp_uid ); ?>-name" name="spr_name" required maxlength="100" autocomplete="name">
		</p>

		<p class="spr-enquiry__field">
			<label for="<?php echo esc_attr( $sp_uid ); ?>-phone"><?php esc_html_e( 'Phone Number', 'sp-realtors' ); ?> <span aria-hidden="true">*</span></label>
			<input type="tel" id="<?php echo esc_attr( $sp_uid ); ?>-phone" name="spr_phone" required pattern="[0-9+\-\s\(\)]{10,20}" inputmode="tel" autocomplete="tel">
		</p>

		<?php if ( ! empty( $args['show_email'] ) ) : ?>
			<p class="spr-enquiry__field">
				<label for="<?php echo esc_attr( $sp_uid ); ?>-email"><?php esc_html_e( 'Email (optional)', 'sp-realtors' ); ?></label>
				<input type="email" id="<?php echo esc_attr( $sp_uid ); ?>-email" name="spr_email" autocomplete="email">
			</p>
		<?php endif; ?>

		<?php if ( ! empty( $args['show_message'] ) ) : ?>
			<p class="spr-enquiry__field">
				<label for="<?php echo esc_attr( $sp_uid ); ?>-message">
					<?php echo esc_html( $sp_property ? __( 'Your Requirement', 'sp-realtors' ) : __( 'Your Message', 'sp-realtors' ) ); ?>
				</label>
				<textarea id="<?php echo esc_attr( $sp_uid ); ?>-message" name="spr_message" rows="4" maxlength="2000"></textarea>
			</p>
		<?php endif; ?>

		<div class="spr-enquiry__actions">
			<?php if ( $sp_property ) : ?>
				<button type="submit" class="spr-btn spr-btn--whatsapp">
					<?php echo sp_realtors_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php esc_html_e( 'Send on WhatsApp', 'sp-realtors' ); ?>
				</button>
				<?php if ( $sp_tel ) : ?>
					<a class="spr-btn spr-btn--outline" href="<?php echo esc_url( $sp_tel ); ?>">
						<?php echo sp_realtors_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php esc_html_e( 'Call Now', 'sp-realtors' ); ?>
					</a>
				<?php endif; ?>
			<?php else : ?>
				<button type="submit" class="spr-btn spr-btn--primary"><?php echo esc_html( $args['button_label'] ); ?></button>
			<?php endif; ?>
		</div>
	</form>
</div>
