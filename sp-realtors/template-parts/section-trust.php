<?php
/**
 * Trust strip: compact row of icon + title + one-line text (Home page, under the hero).
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

$sp_items = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$title = sp_realtors_theme_mod( "trust_{$i}_title" );
	if ( '' === trim( (string) $title ) ) {
		continue;
	}
	$sp_items[] = array(
		'icon'  => sp_realtors_theme_mod( "trust_{$i}_icon" ),
		'title' => $title,
		'text'  => sp_realtors_theme_mod( "trust_{$i}_text" ),
	);
}

if ( ! $sp_items ) {
	return;
}
?>
<section class="sp-realtors-trust">
	<div class="sp-realtors-container sp-realtors-trust__grid">
		<?php foreach ( $sp_items as $sp_item ) : ?>
			<div class="sp-realtors-trust__item">
				<span class="sp-realtors-trust__icon"><?php echo sp_realtors_icon( $sp_item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="sp-realtors-trust__body">
					<strong><?php echo esc_html( $sp_item['title'] ); ?></strong>
					<?php if ( $sp_item['text'] ) : ?>
						<small><?php echo esc_html( $sp_item['text'] ); ?></small>
					<?php endif; ?>
				</span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
