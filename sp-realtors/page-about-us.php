<?php
/**
 * About page: hero (page title + content) → stats → approach → why choose us → CTA.
 *
 * Template for the page with slug "about-us" (WordPress template hierarchy).
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$sp_image_id = has_post_thumbnail() ? get_post_thumbnail_id() : 0;
	?>

	<main id="primary" class="site-main">
		<?php
		get_template_part(
			'template-parts/hero',
			null,
			array(
				'heading'  => get_the_title(),
				'content'  => get_the_content(),
				'image_id' => $sp_image_id,
			)
		);
		?>

		<?php get_template_part( 'template-parts/section-stats' ); ?>
		<?php get_template_part( 'template-parts/section-approach' ); ?>
		<?php get_template_part( 'template-parts/section-why' ); ?>
		<?php get_template_part( 'template-parts/section-cta' ); ?>
	</main>

	<?php
endwhile;

get_footer();
