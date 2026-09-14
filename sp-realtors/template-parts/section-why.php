<?php
/**
 * "Why Choose SP REALTORS?" grid (About page).
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

$sp_items = array();
for ( $i = 1; $i <= 5; $i++ ) {
	$title = sp_realtors_theme_mod( "why_{$i}_title" );
	if ( '' === trim( (string) $title ) ) {
		continue;
	}
	$sp_items[] = array(
		'icon'  => sp_realtors_theme_mod( "why_{$i}_icon" ),
		'title' => $title,
		'text'  => sp_realtors_theme_mod( "why_{$i}_text" ),
	);
}

if ( ! $sp_items ) {
	return;
}
?>
<section class="sp-realtors-section sp-realtors-section--why">
	<div class="sp-realtors-container">
		<div class="sp-realtors-section__header">
			<?php
			/* translators: site title used in the About page "Why Choose Us" heading */
			printf( '<h2>%s</h2>', esc_html( sprintf( __( 'Why Choose %s?', 'sp-realtors' ), spr_get_business( 'name' ) ) ) );
			?>
		</div>

		<div class="sp-realtors-why__grid">
			<?php foreach ( $sp_items as $sp_item ) : ?>
				<div class="sp-realtors-why__item">
					<span class="sp-realtors-why__icon"><?php echo sp_realtors_icon( $sp_item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<strong><?php echo esc_html( $sp_item['title'] ); ?></strong>
					<?php if ( $sp_item['text'] ) : ?>
						<small><?php echo esc_html( $sp_item['text'] ); ?></small>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
