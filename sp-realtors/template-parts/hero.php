<?php
/**
 * Two-column hero: heading + text on the left, image (with a small floating
 * badge) on the right. Used on the front page and the About page.
 *
 * @package SP_Realtors
 *
 * @var array $args {
 *     @type string $heading  Required.
 *     @type string $content  Optional pre-escaped HTML (e.g. the_content()). Falls back to hero subtext.
 *     @type int    $image_id Optional attachment ID. Falls back to the Customizer hero image.
 *     @type string $badge    Optional floating badge text. Falls back to the Customizer hero eyebrow.
 * }
 */

defined( 'ABSPATH' ) || exit;

$sp_heading  = isset( $args['heading'] ) ? $args['heading'] : sp_realtors_theme_mod( 'hero_heading' );
$sp_content  = isset( $args['content'] ) ? $args['content'] : '';
$sp_image_id = isset( $args['image_id'] ) && $args['image_id'] ? absint( $args['image_id'] ) : absint( sp_realtors_theme_mod( 'hero_image' ) );
$sp_badge    = isset( $args['badge'] ) ? $args['badge'] : sp_realtors_theme_mod( 'hero_eyebrow' );
?>
<section class="sp-realtors-hero">
	<div class="sp-realtors-hero__inner sp-realtors-container">
		<div class="sp-realtors-hero__text">
			<h1 class="sp-realtors-hero__heading"><?php echo esc_html( $sp_heading ); ?></h1>
			<?php if ( $sp_content ) : ?>
				<div class="sp-realtors-hero__content"><?php echo wp_kses_post( $sp_content ); ?></div>
			<?php else : ?>
				<p class="sp-realtors-hero__subtext"><?php echo esc_html( sp_realtors_theme_mod( 'hero_subtext' ) ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $args['show_search'] ) && sp_realtors_core_active() ) : ?>
				<?php get_template_part( 'template-parts/search-card' ); ?>
			<?php endif; ?>
		</div>

		<div class="sp-realtors-hero__media">
			<?php if ( $sp_image_id ) : ?>
				<?php echo wp_get_attachment_image( $sp_image_id, 'spr-hero', false, array( 'class' => 'sp-realtors-hero__image' ) ); ?>
			<?php endif; ?>
			<?php if ( $sp_badge ) : ?>
				<span class="sp-realtors-hero__badge"><?php echo esc_html( $sp_badge ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</section>
