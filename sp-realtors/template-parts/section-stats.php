<?php
/**
 * Stat row (About page): 4 x number + label.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

$sp_items = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$number = sp_realtors_theme_mod( "stat_{$i}_number" );
	if ( '' === trim( (string) $number ) ) {
		continue;
	}
	$sp_items[] = array(
		'number' => $number,
		'label'  => sp_realtors_theme_mod( "stat_{$i}_label" ),
	);
}

if ( ! $sp_items ) {
	return;
}
?>
<section class="sp-realtors-stats">
	<div class="sp-realtors-container sp-realtors-stats__grid">
		<?php foreach ( $sp_items as $sp_item ) : ?>
			<div class="sp-realtors-stats__item">
				<strong><?php echo esc_html( $sp_item['number'] ); ?></strong>
				<span><?php echo esc_html( $sp_item['label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
