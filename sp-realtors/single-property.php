<?php
/**
 * Single property: gallery + sticky enquiry card, specs, highlights,
 * description, amenities, map, related properties.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! sp_realtors_core_active() ) {
	get_template_part( 'template-parts/content-none' );
	get_footer();
	return;
}

while ( have_posts() ) :
	the_post();
	$sp_id = get_the_ID();
	$sp_p  = spr_get_property( $sp_id );
	?>

	<main id="primary" class="site-main sp-realtors-container">
		<?php sp_realtors_breadcrumbs(); ?>

		<div class="spr-property-layout">
			<div class="spr-property-main">
				<?php
				get_template_part(
					'template-parts/property-gallery',
					null,
					array(
						'gallery' => $sp_p['gallery'],
						'alt'     => spr_plain_text( $sp_p['title'] ),
					)
				);
				?>

				<header class="spr-property-header">
					<?php if ( $sp_p['status_label'] ) : ?>
						<span class="spr-card__badge spr-card__badge--<?php echo esc_attr( $sp_p['status'] ); ?>"><?php echo esc_html( $sp_p['status_label'] ); ?></span>
					<?php endif; ?>
					<h1><?php echo esc_html( spr_plain_text( $sp_p['title'] ) ); ?></h1>
					<?php if ( $sp_p['location'] ) : ?>
						<p class="spr-property-header__location">
							<?php echo sp_realtors_icon( 'location' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php echo esc_html( $sp_p['location'] ); ?>
						</p>
					<?php endif; ?>
					<p class="spr-property-header__price"><?php echo esc_html( $sp_p['price_formatted'] ); ?></p>

					<ul class="spr-property-specs">
						<?php if ( $sp_p['area'] ) : ?>
							<li><?php echo sp_realtors_icon( 'area' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( number_format_i18n( $sp_p['area'] ) . ' ' . $sp_p['area_unit'] ); ?></li>
						<?php endif; ?>
						<?php if ( $sp_p['bedrooms'] ) : ?>
							<li><?php echo sp_realtors_icon( 'bed' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( sprintf( /* translators: %d: bedrooms */ _n( '%d Bedroom', '%d Bedrooms', $sp_p['bedrooms'], 'sp-realtors' ), $sp_p['bedrooms'] ) ); ?></li>
						<?php endif; ?>
						<?php if ( $sp_p['bathrooms'] ) : ?>
							<li><?php echo sp_realtors_icon( 'bath' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( sprintf( /* translators: %d: bathrooms */ _n( '%d Bathroom', '%d Bathrooms', $sp_p['bathrooms'], 'sp-realtors' ), $sp_p['bathrooms'] ) ); ?></li>
						<?php endif; ?>
						<?php if ( $sp_p['furnishing_label'] ) : ?>
							<li><?php echo esc_html( $sp_p['furnishing_label'] ); ?></li>
						<?php endif; ?>
					</ul>
				</header>

				<?php if ( $sp_p['highlights'] ) : ?>
					<section class="spr-property-section">
						<h2><?php esc_html_e( 'Property Highlights', 'sp-realtors' ); ?></h2>
						<ul class="spr-highlights">
							<?php foreach ( $sp_p['highlights'] as $sp_line ) : ?>
								<li><?php echo sp_realtors_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( $sp_line ); ?></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php if ( trim( (string) get_the_content() ) ) : ?>
					<section class="spr-property-section entry-content">
						<h2><?php esc_html_e( 'Description', 'sp-realtors' ); ?></h2>
						<?php the_content(); ?>
					</section>
				<?php endif; ?>

				<?php if ( $sp_p['amenities'] ) : ?>
					<section class="spr-property-section">
						<h2><?php esc_html_e( 'Amenities', 'sp-realtors' ); ?></h2>
						<ul class="spr-amenities">
							<?php foreach ( $sp_p['amenities'] as $sp_key => $sp_label ) : ?>
								<li>
									<span class="spr-amenities__icon"><?php echo sp_realtors_icon( sp_realtors_amenity_icon( $sp_key ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
									<span><?php echo esc_html( $sp_label ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php if ( $sp_p['address'] || $sp_p['map_url'] ) : ?>
					<section class="spr-property-section">
						<h2><?php esc_html_e( 'Location', 'sp-realtors' ); ?></h2>
						<?php if ( $sp_p['address'] ) : ?>
							<p class="spr-property-address"><?php echo sp_realtors_icon( 'location' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo nl2br( esc_html( $sp_p['address'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
						<?php endif; ?>
						<?php if ( $sp_p['map_url'] ) : ?>
							<div class="spr-map-wrap"><?php echo spr_get_map_embed( $sp_id, spr_plain_text( $sp_p['title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
							<a class="spr-btn spr-btn--outline" href="<?php echo esc_url( $sp_p['map_url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( 'View on Google Maps', 'sp-realtors' ); ?>
							</a>
						<?php endif; ?>
					</section>
				<?php endif; ?>

				<?php
				$sp_related = spr_related_properties_query( $sp_id, 3 );
				if ( $sp_related->have_posts() ) :
					?>
					<section class="spr-property-section">
						<h2><?php esc_html_e( 'Related Properties', 'sp-realtors' ); ?></h2>
						<div class="spr-grid spr-grid--3">
							<?php
							while ( $sp_related->have_posts() ) :
								$sp_related->the_post();
								spr_get_template_part( 'card-property', array( 'post_id' => get_the_ID(), 'heading' => 'h3' ) );
							endwhile;
							wp_reset_postdata();
							?>
						</div>
					</section>
					<?php
				endif;
				?>
			</div>

			<aside class="spr-property-aside">
				<div class="spr-enquiry-card">
					<?php
					echo spr_render_enquiry_form( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'source'      => 'property',
							'property_id' => $sp_id,
							'title'       => __( 'Enquire About This Property', 'sp-realtors' ),
						)
					);
					?>
				</div>
			</aside>
		</div>
	</main>

	<?php
endwhile;

get_footer();
