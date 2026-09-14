<?php
/**
 * Contact page: heading → info cards + enquiry form → map.
 *
 * Template for the page with slug "contact-us" (WordPress template hierarchy).
 * A Customizer shortcode override (Contact Page section) replaces the built-in
 * form when set — e.g. to paste a Contact Form 7 shortcode.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$sp_shortcode = trim( (string) sp_realtors_theme_mod( 'contact_form_shortcode' ) );
	$sp_map_url   = trim( (string) sp_realtors_theme_mod( 'contact_map_url' ) );
	?>

	<main id="primary" class="site-main sp-realtors-container">
		<?php sp_realtors_breadcrumbs(); ?>

		<header class="sp-realtors-page-header">
			<h1><?php the_title(); ?></h1>
			<?php if ( trim( (string) get_the_content() ) ) : ?>
				<div class="sp-realtors-page-header__content"><?php the_content(); ?></div>
			<?php endif; ?>
		</header>

		<div class="sp-realtors-contact">
			<?php if ( sp_realtors_core_active() ) : ?>
				<div class="sp-realtors-contact__info">
					<ul>
						<?php if ( spr_get_business( 'phone' ) ) : ?>
							<li>
								<span class="sp-realtors-contact__icon"><?php echo sp_realtors_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span>
									<strong><?php esc_html_e( 'Call Us', 'sp-realtors' ); ?></strong>
									<a href="<?php echo esc_url( spr_tel_url() ); ?>"><?php echo esc_html( spr_get_business( 'phone' ) ); ?></a>
								</span>
							</li>
						<?php endif; ?>
						<?php if ( spr_get_business( 'email' ) ) : ?>
							<li>
								<span class="sp-realtors-contact__icon"><?php echo sp_realtors_icon( 'email' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span>
									<strong><?php esc_html_e( 'Email Us', 'sp-realtors' ); ?></strong>
									<a href="mailto:<?php echo esc_attr( spr_get_business( 'email' ) ); ?>"><?php echo esc_html( spr_get_business( 'email' ) ); ?></a>
								</span>
							</li>
						<?php endif; ?>
						<?php if ( spr_get_business( 'address' ) ) : ?>
							<li>
								<span class="sp-realtors-contact__icon"><?php echo sp_realtors_icon( 'location' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span>
									<strong><?php esc_html_e( 'Visit Our Office', 'sp-realtors' ); ?></strong>
									<?php echo esc_html( spr_get_business( 'address' ) ); ?>
								</span>
							</li>
						<?php endif; ?>
						<?php if ( spr_get_business( 'hours' ) ) : ?>
							<li>
								<span class="sp-realtors-contact__icon"><?php echo sp_realtors_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span>
									<strong><?php esc_html_e( 'Working Hours', 'sp-realtors' ); ?></strong>
									<?php echo esc_html( spr_get_business( 'hours' ) ); ?>
								</span>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>

			<div class="sp-realtors-contact__form">
				<?php
				if ( $sp_shortcode ) {
					echo do_shortcode( $sp_shortcode );
				} elseif ( sp_realtors_core_active() ) {
					echo spr_render_enquiry_form(
						array(
							'source'       => 'contact',
							'title'        => __( 'Send Us a Message', 'sp-realtors' ),
							'button_label' => __( 'Send Message', 'sp-realtors' ),
						)
					);
				}
				?>
			</div>
		</div>

		<?php if ( sp_realtors_core_active() && $sp_map_url ) : ?>
			<div class="sp-realtors-contact__map">
				<?php echo spr_get_map_embed( $sp_map_url, __( 'Our office location', 'sp-realtors' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
	</main>

	<?php
endwhile;

get_footer();
