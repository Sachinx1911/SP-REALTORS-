<?php
/**
 * 404 Not Found.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main sp-realtors-container">
	<h1 class="page-title"><?php esc_html_e( 'Page not found', 'sp-realtors' ); ?></h1>
	<p><?php esc_html_e( 'The page you are looking for does not exist or has moved.', 'sp-realtors' ); ?></p>

	<p>
		<a class="spr-btn spr-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( 'Back to Home', 'sp-realtors' ); ?>
		</a>
		<?php if ( sp_realtors_core_active() ) : ?>
			<a class="spr-btn spr-btn--outline" href="<?php echo esc_url( spr_get_properties_url() ); ?>">
				<?php esc_html_e( 'Browse Properties', 'sp-realtors' ); ?>
			</a>
		<?php endif; ?>
	</p>

	<?php get_search_form(); ?>
</main>

<?php get_footer(); ?>
