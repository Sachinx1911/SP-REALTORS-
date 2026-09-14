<?php
/**
 * "Our Approach" steps (About page): icon + title, connected by arrows.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

$sp_icons = array( 'search', 'grid', 'compare', 'headset' );
$sp_items = array();

for ( $i = 1; $i <= 4; $i++ ) {
	$title = sp_realtors_theme_mod( "approach_{$i}_title" );
	if ( '' === trim( (string) $title ) ) {
		continue;
	}
	$sp_items[] = array(
		'icon'  => $sp_icons[ $i - 1 ],
		'title' => $title,
		'text'  => sp_realtors_theme_mod( "approach_{$i}_text" ),
	);
}

if ( ! $sp_items ) {
	return;
}
?>
<section class="sp-realtors-section sp-realtors-section--approach">
	<div class="sp-realtors-container">
		<div class="sp-realtors-section__header">
			<h2><?php esc_html_e( 'Our Approach', 'sp-realtors' ); ?></h2>
		</div>

		<div class="sp-realtors-approach">
			<?php foreach ( $sp_items as $sp_i => $sp_item ) : ?>
				<?php if ( $sp_i > 0 ) : ?>
					<span class="sp-realtors-approach__arrow" aria-hidden="true"><?php echo sp_realtors_icon( 'chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
				<div class="sp-realtors-approach__step">
					<span class="sp-realtors-approach__icon"><?php echo sp_realtors_icon( $sp_item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<strong><?php echo esc_html( $sp_item['title'] ); ?></strong>
					<?php if ( $sp_item['text'] ) : ?>
						<small><?php echo esc_html( $sp_item['text'] ); ?></small>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
