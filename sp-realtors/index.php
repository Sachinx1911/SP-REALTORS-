<?php
/**
 * Default template: blog post listing / fallback for anything without a
 * more specific template. Full section-by-section homepage design is
 * front-page.php (Phase 5) — this file only needs to work, not look final.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main sp-realtors-container">
	<?php sp_realtors_breadcrumbs(); ?>

	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article <?php post_class(); ?>>
				<header class="entry-header">
					<?php
					if ( is_singular() ) {
						the_title( '<h1 class="entry-title">', '</h1>' );
					} else {
						the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
					}
					?>
					<?php sp_realtors_posted_on(); ?>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="entry-thumbnail"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php endif; ?>

				<div class="entry-content">
					<?php the_excerpt(); ?>
				</div>
			</article>
		<?php endwhile; ?>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content-none' ); ?>
	<?php endif; ?>
</main>

<?php
if ( is_active_sidebar( 'blog-sidebar' ) ) {
	get_sidebar();
}
get_footer();
