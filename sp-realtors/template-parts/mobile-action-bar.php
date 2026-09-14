<?php
/**
 * Sticky Call / WhatsApp bar shown only on small screens.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

if ( ! sp_realtors_theme_mod( 'mobile_sticky_bar' ) ) {
	return;
}
if ( ! sp_realtors_core_active() ) {
	return;
}

$sp_tel      = spr_tel_url();
$sp_whatsapp = spr_whatsapp_url();

if ( ! $sp_tel && ! $sp_whatsapp ) {
	return;
}
?>
<div class="sp-realtors-mobile-bar">
	<?php if ( $sp_tel ) : ?>
		<a class="sp-realtors-mobile-bar__item sp-realtors-mobile-bar__item--call" href="<?php echo esc_url( $sp_tel ); ?>">
			<?php echo sp_realtors_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( 'Call', 'sp-realtors' ); ?></span>
		</a>
	<?php endif; ?>
	<?php if ( $sp_whatsapp ) : ?>
		<a class="sp-realtors-mobile-bar__item sp-realtors-mobile-bar__item--whatsapp" href="<?php echo esc_url( $sp_whatsapp ); ?>" target="_blank" rel="noopener noreferrer">
			<?php echo sp_realtors_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( 'WhatsApp', 'sp-realtors' ); ?></span>
		</a>
	<?php endif; ?>
</div>
