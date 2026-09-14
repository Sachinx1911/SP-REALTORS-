<?php
/**
 * Property gallery: large image + thumbnail strip (main.js swaps the image;
 * works with JS off since every thumbnail is a real link to the full image).
 *
 * @package SP_Realtors
 *
 * @var array $args { @type int[] $gallery Attachment IDs. @type string $alt Base alt text. }
 */

defined( 'ABSPATH' ) || exit;

$sp_ids = isset( $args['gallery'] ) ? $args['gallery'] : array();
$sp_alt = isset( $args['alt'] ) ? $args['alt'] : '';

if ( ! $sp_ids ) {
	return;
}

$sp_first = wp_get_attachment_image_src( $sp_ids[0], 'spr-gallery' );
?>
<div class="spr-gallery">
	<a class="spr-gallery__main" href="<?php echo esc_url( $sp_first ? $sp_first[0] : '' ); ?>">
		<?php echo wp_get_attachment_image( $sp_ids[0], 'spr-gallery', false, array( 'alt' => $sp_alt ) ); ?>
	</a>

	<?php if ( count( $sp_ids ) > 1 ) : ?>
		<div class="spr-gallery__thumbs">
			<?php foreach ( $sp_ids as $sp_i => $sp_id ) : ?>
				<?php
				$sp_full  = wp_get_attachment_image_src( $sp_id, 'spr-gallery' );
				$sp_srcset = wp_get_attachment_image_srcset( $sp_id, 'spr-gallery' );
				?>
				<button
					type="button"
					class="spr-gallery__thumb"
					data-full="<?php echo esc_url( $sp_full ? $sp_full[0] : '' ); ?>"
					data-srcset="<?php echo esc_attr( $sp_srcset ? $sp_srcset : '' ); ?>"
					data-alt="<?php echo esc_attr( $sp_alt ); ?>"
					aria-current="<?php echo 0 === $sp_i ? 'true' : 'false'; ?>"
				>
					<?php echo wp_get_attachment_image( $sp_id, 'spr-thumb', false, array( 'alt' => '' ) ); ?>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
