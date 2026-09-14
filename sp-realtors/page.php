<?php
/**
 * Generic page template.
 *
 * Dedicated designs: page-about-us.php, page-contact-us.php (Phase 5).
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main sp-realtors-container">
	<?php sp_realtors_breadcrumbs(); ?>

	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="entry-thumbnail"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>

		<?php if ( comments_open() || get_comments_number() ) : ?>
			<?php comments_template(); ?>
		<?php endif; ?>
	<?php endwhile; ?>
</main>

<?php get_footer(); ?>
