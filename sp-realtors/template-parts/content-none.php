<?php
/**
 * Shown when a query returns no results.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="sp-realtors-empty">
	<?php if ( is_search() ) : ?>
		<p>
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'No results found for “%s”. Try a different search.', 'sp-realtors' ),
				esc_html( get_search_query() )
			);
			?>
		</p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found here yet.', 'sp-realtors' ); ?></p>
	<?php endif; ?>
</div>
