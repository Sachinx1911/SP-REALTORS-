<?php
/**
 * Footer: brand, footer menu, contact block, social, widgets, copyright.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;
?>
	<footer id="colophon" class="sp-realtors-footer">
		<div class="sp-realtors-footer__inner">

			<div class="sp-realtors-footer__brand">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<p class="sp-realtors-footer__site-title"><?php bloginfo( 'name' ); ?></p>
				<?php endif; ?>
				<p class="sp-realtors-footer__tagline"><?php echo esc_html( sp_realtors_theme_mod( 'footer_tagline' ) ); ?></p>
				<?php sp_realtors_social_links(); ?>
			</div>

			<nav class="sp-realtors-footer__menu" aria-label="<?php esc_attr_e( 'Footer', 'sp-realtors' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'items_wrap'     => '<ul id="footer-menu" class="sp-realtors-footer__menu-list">%3$s</ul>',
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>

			<?php if ( sp_realtors_core_active() ) : ?>
				<div class="sp-realtors-footer__contact">
					<h2 class="widget-title"><?php esc_html_e( 'Contact', 'sp-realtors' ); ?></h2>
					<ul>
						<?php if ( spr_get_business( 'address' ) ) : ?>
							<li><?php echo sp_realtors_icon( 'location' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( spr_get_business( 'address' ) ); ?></li>
						<?php endif; ?>
						<?php if ( spr_get_business( 'phone' ) ) : ?>
							<li><a href="<?php echo esc_url( spr_tel_url() ); ?>"><?php echo sp_realtors_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( spr_get_business( 'phone' ) ); ?></a></li>
						<?php endif; ?>
						<?php if ( spr_get_business( 'email' ) ) : ?>
							<li><a href="mailto:<?php echo esc_attr( spr_get_business( 'email' ) ); ?>"><?php echo sp_realtors_icon( 'email' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( spr_get_business( 'email' ) ); ?></a></li>
						<?php endif; ?>
						<?php if ( spr_get_business( 'hours' ) ) : ?>
							<li><?php echo esc_html( spr_get_business( 'hours' ) ); ?></li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
				<div class="sp-realtors-footer__widgets sp-realtors-footer__widgets--1">
					<?php dynamic_sidebar( 'footer-1' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
				<div class="sp-realtors-footer__widgets sp-realtors-footer__widgets--2">
					<?php dynamic_sidebar( 'footer-2' ); ?>
				</div>
			<?php endif; ?>

		</div>

		<div class="sp-realtors-footer__bottom">
			<p class="sp-realtors-footer__copyright">
				&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <span class="sp-realtors-footer__copyright-text"><?php echo esc_html( sp_realtors_theme_mod( 'footer_copyright' ) ); ?></span>
			</p>
		</div>
	</footer>

	<?php get_template_part( 'template-parts/mobile-action-bar' ); ?>

	<?php wp_footer(); ?>
</body>
</html>
