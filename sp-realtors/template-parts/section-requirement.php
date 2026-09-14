<?php
/**
 * "Browse by Requirement" cards (Home page).
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

$sp_icons = array( 'home', 'key', 'building', 'store' );
$sp_items = array();

for ( $i = 1; $i <= 4; $i++ ) {
	$title = sp_realtors_theme_mod( "requirement_{$i}_title" );
	if ( '' === trim( (string) $title ) ) {
		continue;
	}
	$sp_items[] = array(
		'icon'  => $sp_icons[ $i - 1 ],
		'title' => $title,
		'text'  => sp_realtors_theme_mod( "requirement_{$i}_text" ),
		'image' => absint( sp_realtors_theme_mod( "requirement_{$i}_image" ) ),
		'url'   => sp_realtors_theme_mod( "requirement_{$i}_url" ),
	);
}

if ( ! $sp_items ) {
	return;
}
?>
<section class="sp-realtors-section sp-realtors-section--requirement">
	<div class="sp-realtors-container">
		<div class="sp-realtors-section__header">
			<h2><?php esc_html_e( 'Browse by Requirement', 'sp-realtors' ); ?></h2>
		</div>

		<div class="spr-requirement-grid">
			<?php foreach ( $sp_items as $sp_item ) : ?>
				<?php
				$sp_tag = $sp_item['url'] ? 'a' : 'div';
				?>
				<<?php echo esc_attr( $sp_tag ); ?>
					class="spr-requirement-card"
					<?php echo $sp_item['url'] ? 'href="' . esc_url( $sp_item['url'] ) . '"' : ''; ?>
				>
					<?php if ( $sp_item['image'] ) : ?>
						<span class="spr-requirement-card__image"><?php echo wp_get_attachment_image( $sp_item['image'], 'spr-thumb' ); ?></span>
					<?php else : ?>
						<span class="spr-requirement-card__icon"><?php echo sp_realtors_icon( $sp_item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
					<strong><?php echo esc_html( $sp_item['title'] ); ?></strong>
					<?php if ( $sp_item['text'] ) : ?>
						<small><?php echo esc_html( $sp_item['text'] ); ?></small>
					<?php endif; ?>
				</<?php echo esc_attr( $sp_tag ); ?>>
			<?php endforeach; ?>
		</div>
	</div>
</section>
