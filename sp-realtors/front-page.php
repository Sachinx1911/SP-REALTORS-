<?php
/**
 * Home page: hero + search → trust strip → featured properties →
 * browse by requirement → CTA banner.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main">
	<?php get_template_part( 'template-parts/hero', null, array( 'show_search' => true ) ); ?>
	<?php get_template_part( 'template-parts/section-trust' ); ?>
	<?php get_template_part( 'template-parts/section-featured' ); ?>
	<?php get_template_part( 'template-parts/section-requirement' ); ?>

	<?php
	// A static front page can still have its own block content (optional).
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			if ( trim( (string) get_the_content() ) ) :
				?>
				<section class="sp-realtors-section sp-realtors-container entry-content">
					<?php the_content(); ?>
				</section>
				<?php
			endif;
		endwhile;
	endif;
	?>

	<?php get_template_part( 'template-parts/section-cta' ); ?>
</main>

<?php get_footer(); ?>
