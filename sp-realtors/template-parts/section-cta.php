<?php
/**
 * CTA banner: heading + text + WhatsApp button.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

$sp_url = sp_realtors_core_active() ? spr_whatsapp_url() : '';
?>
<section class="sp-realtors-cta">
	<div class="sp-realtors-container sp-realtors-cta__inner">
		<div>
			<h2><?php echo esc_html( sp_realtors_theme_mod( 'cta_heading' ) ); ?></h2>
			<p><?php echo esc_html( sp_realtors_theme_mod( 'cta_text' ) ); ?></p>
		</div>
		<?php if ( $sp_url ) : ?>
			<a class="spr-btn spr-btn--whatsapp" href="<?php echo esc_url( $sp_url ); ?>" target="_blank" rel="noopener noreferrer">
				<?php echo sp_realtors_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo esc_html( sp_realtors_theme_mod( 'cta_button_label' ) ); ?>
			</a>
		<?php endif; ?>
	</div>
</section>
