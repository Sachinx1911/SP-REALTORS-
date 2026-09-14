<?php
/**
 * Featured Properties section (Home page).
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

if ( ! sp_realtors_core_active() ) {
	return;
}

$sp_query = spr_featured_properties_query( 3 );
if ( ! $sp_query->have_posts() ) {
	return;
}
?>
<section class="sp-realtors-section sp-realtors-section--featured">
	<div class="sp-realtors-container">
		<div class="sp-realtors-section__header">
			<h2><?php esc_html_e( 'Featured Properties', 'sp-realtors' ); ?></h2>
			<a class="sp-realtors-section__link" href="<?php echo esc_url( spr_get_properties_url() ); ?>">
				<?php esc_html_e( 'View All Properties', 'sp-realtors' ); ?>
				<?php echo sp_realtors_icon( 'chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>

		<div class="spr-grid spr-grid--3">
			<?php
			while ( $sp_query->have_posts() ) :
				$sp_query->the_post();
				spr_get_template_part( 'card-property', array( 'post_id' => get_the_ID(), 'heading' => 'h3' ) );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
