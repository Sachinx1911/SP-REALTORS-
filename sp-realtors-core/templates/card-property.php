<?php
/**
 * Fallback property card (used only if the active theme has no template-parts/card-property.php).
 *
 * Theme override: copy to {theme}/template-parts/card-property.php
 *
 * @package SP_Realtors_Core
 *
 * @var array $args {
 *     @type int    $post_id Property ID.
 *     @type string $heading Heading tag: h2|h3|h4.
 * }
 */

defined( 'ABSPATH' ) || exit;

$spr_post_id = isset( $args['post_id'] ) ? absint( $args['post_id'] ) : get_the_ID();
$spr_p       = spr_get_property( $spr_post_id );
if ( ! $spr_p ) {
	return;
}
$spr_heading = ( isset( $args['heading'] ) && in_array( $args['heading'], array( 'h2', 'h3', 'h4' ), true ) ) ? $args['heading'] : 'h3';
?>
<article <?php post_class( 'spr-card', $spr_post_id ); ?>>
	<a class="spr-card__media" href="<?php echo esc_url( $spr_p['permalink'] ); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( $spr_p['thumbnail_id'] ) {
			echo wp_get_attachment_image( $spr_p['thumbnail_id'], 'medium_large', false, array( 'loading' => 'lazy' ) );
		}
		?>
		<?php if ( $spr_p['status_label'] ) : ?>
			<span class="spr-card__badge spr-card__badge--<?php echo esc_attr( $spr_p['status'] ); ?>"><?php echo esc_html( $spr_p['status_label'] ); ?></span>
		<?php endif; ?>
	</a>

	<div class="spr-card__body">
		<<?php echo esc_attr( $spr_heading ); ?> class="spr-card__title">
			<a href="<?php echo esc_url( $spr_p['permalink'] ); ?>"><?php echo esc_html( spr_plain_text( $spr_p['title'] ) ); ?></a>
		</<?php echo esc_attr( $spr_heading ); ?>>

		<?php if ( $spr_p['location'] ) : ?>
			<p class="spr-card__location"><?php echo esc_html( $spr_p['location'] ); ?></p>
		<?php endif; ?>

		<p class="spr-card__price"><?php echo esc_html( $spr_p['price_formatted'] ); ?></p>

		<ul class="spr-card__specs">
			<?php if ( $spr_p['area'] ) : ?>
				<li><?php echo esc_html( number_format_i18n( $spr_p['area'] ) . ' ' . $spr_p['area_unit'] ); ?></li>
			<?php endif; ?>
			<?php if ( $spr_p['bedrooms'] ) : ?>
				<li>
					<?php
					/* translators: %d: number of bedrooms */
					echo esc_html( sprintf( _n( '%d Bed', '%d Beds', $spr_p['bedrooms'], 'sp-realtors-core' ), $spr_p['bedrooms'] ) );
					?>
				</li>
			<?php endif; ?>
			<?php if ( $spr_p['bathrooms'] ) : ?>
				<li>
					<?php
					/* translators: %d: number of bathrooms */
					echo esc_html( sprintf( _n( '%d Bath', '%d Baths', $spr_p['bathrooms'], 'sp-realtors-core' ), $spr_p['bathrooms'] ) );
					?>
				</li>
			<?php endif; ?>
			<?php if ( $spr_p['furnishing_label'] ) : ?>
				<li><?php echo esc_html( $spr_p['furnishing_label'] ); ?></li>
			<?php endif; ?>
		</ul>

		<div class="spr-card__actions">
			<a class="spr-btn spr-btn--outline" href="<?php echo esc_url( $spr_p['permalink'] ); ?>">
				<?php esc_html_e( 'View Details', 'sp-realtors-core' ); ?>
				<span class="screen-reader-text"><?php echo esc_html( ': ' . spr_plain_text( $spr_p['title'] ) ); ?></span>
			</a>
			<?php if ( $spr_p['whatsapp_url'] ) : ?>
				<a class="spr-btn spr-btn--whatsapp" href="<?php echo esc_url( $spr_p['whatsapp_url'] ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'WhatsApp', 'sp-realtors-core' ); ?>
					<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'sp-realtors-core' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</article>
