<?php
/**
 * Property archive: breadcrumbs, filters (sidebar/drawer), sort, grid, load more.
 *
 * Reused by taxonomy-property_location.php for /location/{slug}/ archives.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

get_header();

$sp_filters = sp_realtors_core_active() ? spr_get_filter_values() : array( 'sort' => 'latest' );
$sp_sorts   = sp_realtors_core_active() ? spr_get_sort_options() : array();
?>

<main id="primary" class="site-main sp-realtors-container">
	<?php sp_realtors_breadcrumbs(); ?>

	<header class="sp-realtors-page-header">
		<?php if ( is_tax() ) : ?>
			<h1><?php single_term_title(); ?></h1>
			<?php
			$sp_term_description = term_description();
			if ( $sp_term_description ) {
				echo '<div class="sp-realtors-page-header__content">' . wp_kses_post( $sp_term_description ) . '</div>';
			}
			?>
		<?php else : ?>
			<h1><?php esc_html_e( 'Properties', 'sp-realtors' ); ?></h1>
			<p class="sp-realtors-page-header__content"><?php esc_html_e( 'Explore residential and commercial properties in Navi Mumbai and nearby areas.', 'sp-realtors' ); ?></p>
		<?php endif; ?>
	</header>

	<?php if ( sp_realtors_core_active() ) : ?>
		<button type="button" class="spr-filters-toggle spr-btn spr-btn--outline">
			<?php echo sp_realtors_icon( 'grid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php esc_html_e( 'Filters', 'sp-realtors' ); ?>
		</button>
	<?php endif; ?>

	<div class="spr-archive-layout">
		<?php if ( sp_realtors_core_active() ) : ?>
			<aside class="spr-filters-panel" id="spr-filters-panel">
				<?php get_template_part( 'template-parts/filter-sidebar' ); ?>
			</aside>
			<div class="spr-filters-overlay" hidden></div>
		<?php endif; ?>

		<div class="spr-archive-main">
			<div class="spr-archive-toolbar">
				<p class="spr-archive-toolbar__count">
					<?php
					printf(
						/* translators: %s: number of properties found */
						esc_html( _n( 'Showing %s property', 'Showing %s properties', $wp_query->found_posts, 'sp-realtors' ) ),
						esc_html( number_format_i18n( $wp_query->found_posts ) )
					);
					?>
				</p>

				<?php if ( $sp_sorts ) : ?>
					<form class="spr-archive-toolbar__sort" method="get" action="<?php echo esc_url( spr_get_properties_url() ); ?>">
						<?php foreach ( array( 'location', 'ptype', 'purpose', 'config', 'budget' ) as $sp_key ) : ?>
							<?php foreach ( (array) $sp_filters[ $sp_key ] as $sp_value ) : ?>
								<?php if ( '' !== $sp_value ) : ?>
									<input type="hidden" name="<?php echo esc_attr( $sp_key ); ?><?php echo is_array( $sp_filters[ $sp_key ] ) ? '[]' : ''; ?>" value="<?php echo esc_attr( $sp_value ); ?>">
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endforeach; ?>
						<label for="spr-sort"><?php esc_html_e( 'Sort by:', 'sp-realtors' ); ?></label>
						<select id="spr-sort" name="sort">
							<?php foreach ( $sp_sorts as $sp_key => $sp_label ) : ?>
								<option value="<?php echo esc_attr( $sp_key ); ?>" <?php selected( $sp_filters['sort'], $sp_key ); ?>><?php echo esc_html( $sp_label ); ?></option>
							<?php endforeach; ?>
						</select>
						<noscript><button type="submit" class="spr-btn spr-btn--outline"><?php esc_html_e( 'Apply', 'sp-realtors' ); ?></button></noscript>
					</form>
				<?php endif; ?>
			</div>

			<?php if ( have_posts() ) : ?>
				<div class="spr-grid spr-grid--3" id="spr-property-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						spr_get_template_part( 'card-property', array( 'post_id' => get_the_ID(), 'heading' => 'h2' ) );
					endwhile;
					?>
				</div>

				<div class="spr-load-more">
					<?php if ( $wp_query->max_num_pages > 1 ) : ?>
						<button type="button" class="spr-btn spr-btn--outline spr-load-more__button" data-page="1" data-max-pages="<?php echo esc_attr( $wp_query->max_num_pages ); ?>">
							<?php esc_html_e( 'Load More', 'sp-realtors' ); ?>
						</button>
					<?php endif; ?>
					<noscript>
						<div class="spr-pagination"><?php the_posts_pagination(); ?></div>
					</noscript>
				</div>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content-none' ); ?>
			<?php endif; ?>
		</div>
	</div>
</main>

<?php get_footer(); ?>
