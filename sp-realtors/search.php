<?php
/**
 * Search results.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main sp-realtors-container">
	<?php sp_realtors_breadcrumbs(); ?>

	<h1 class="page-title">
		<?php
		printf(
			/* translators: %s: search query */
			esc_html__( 'Search results for: %s', 'sp-realtors' ),
			'<span>' . esc_html( get_search_query() ) . '</span>'
		);
		?>
	</h1>

	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			if ( 'property' === get_post_type() && sp_realtors_core_active() ) {
				spr_get_template_part( 'card-property', array( 'post_id' => get_the_ID() ) );
				continue;
			}
			?>
			<article <?php post_class(); ?>>
				<header class="entry-header">
					<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
				</header>
				<div class="entry-content"><?php the_excerpt(); ?></div>
			</article>
			<?php
		endwhile;
		?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content-none' ); ?>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
