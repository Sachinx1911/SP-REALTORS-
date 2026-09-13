<?php
/**
 * Fallback testimonial card.
 *
 * Theme override: {theme}/template-parts/card-testimonial.php
 *
 * @package SP_Realtors_Core
 *
 * @var array $args { @type int $post_id Testimonial ID. }
 */

defined( 'ABSPATH' ) || exit;

$spr_post_id = isset( $args['post_id'] ) ? absint( $args['post_id'] ) : get_the_ID();
$spr_rating  = get_post_meta( $spr_post_id, '_spr_rating', true );
$spr_rating  = '' === $spr_rating ? 5 : spr_sanitize_rating( $spr_rating );
$spr_detail  = get_post_meta( $spr_post_id, '_spr_client_meta', true );
?>
<figure class="spr-testimonial">
	<?php /* translators: %d: rating out of 5 */ ?>
	<div class="spr-testimonial__rating" role="img" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %d out of 5', 'sp-realtors-core' ), $spr_rating ) ); ?>">
		<?php echo esc_html( str_repeat( '★', $spr_rating ) . str_repeat( '☆', 5 - $spr_rating ) ); ?>
	</div>
	<blockquote class="spr-testimonial__quote">
		<?php echo wp_kses_post( wpautop( get_post_field( 'post_content', $spr_post_id ) ) ); ?>
	</blockquote>
	<figcaption class="spr-testimonial__author">
		<?php
		if ( has_post_thumbnail( $spr_post_id ) ) {
			echo get_the_post_thumbnail( $spr_post_id, 'thumbnail', array( 'class' => 'spr-testimonial__photo', 'alt' => '' ) );
		}
		?>
		<span>
			<strong><?php echo esc_html( get_the_title( $spr_post_id ) ); ?></strong>
			<?php if ( $spr_detail ) : ?>
				<small><?php echo esc_html( $spr_detail ); ?></small>
			<?php endif; ?>
		</span>
	</figcaption>
</figure>
