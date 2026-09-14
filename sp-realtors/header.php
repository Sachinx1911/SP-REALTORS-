<?php
/**
 * Header: <head>, skip link, sticky site header, primary nav, mobile toggle.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'sp-realtors' ); ?></a>

<header id="masthead" class="sp-realtors-header">
	<div class="sp-realtors-header__inner">

		<div class="sp-realtors-header__brand">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				?>
				<p class="sp-realtors-header__site-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
				</p>
				<?php
			}
			?>
		</div>

		<nav id="site-navigation" class="sp-realtors-nav" aria-label="<?php esc_attr_e( 'Primary', 'sp-realtors' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'container'      => false,
					'items_wrap'     => '<ul id="%1$s" class="sp-realtors-nav__list">%3$s</ul>',
					'fallback_cb'    => 'sp_realtors_menu_fallback',
				)
			);
			?>
		</nav>

		<div class="sp-realtors-header__actions">
			<?php if ( sp_realtors_core_active() && spr_tel_url() ) : ?>
				<a class="sp-realtors-header__call" href="<?php echo esc_url( spr_tel_url() ); ?>">
					<?php echo sp_realtors_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Call us', 'sp-realtors' ); ?></span>
				</a>
			<?php endif; ?>

			<?php if ( sp_realtors_core_active() && spr_whatsapp_url() ) : ?>
				<a class="sp-realtors-header__whatsapp spr-btn spr-btn--whatsapp" href="<?php echo esc_url( spr_whatsapp_url() ); ?>" target="_blank" rel="noopener noreferrer">
					<?php echo sp_realtors_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php esc_html_e( 'WhatsApp', 'sp-realtors' ); ?></span>
				</a>
			<?php endif; ?>

			<button type="button" class="sp-realtors-nav__toggle" aria-expanded="false" aria-controls="site-navigation">
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'sp-realtors' ); ?></span>
				<span class="sp-realtors-nav__toggle-icon" aria-hidden="true">
					<?php echo sp_realtors_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			</button>
		</div>

	</div>
</header>
