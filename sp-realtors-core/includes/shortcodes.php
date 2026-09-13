<?php
/**
 * Shortcodes.
 *
 * WHY HERE: Shortcodes are content features. They keep listings usable
 * inside any page, any theme, or a page builder.
 *
 *   [sp_featured_properties count="3" columns="3"]
 *   [sp_properties location="kharghar" purpose="buy" ptype="" config="" count="6" columns="3" sort="latest"]
 *   [sp_property_search]
 *   [sp_enquiry_form property_id="" title="" show_email="yes" show_message="yes"]
 *   [sp_testimonials count="3"]
 *   [sp_business_info field="phone" link="yes"]
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register shortcodes.
 */
function spr_register_shortcodes() {
	add_shortcode( 'sp_featured_properties', 'spr_shortcode_featured_properties' );
	add_shortcode( 'sp_properties', 'spr_shortcode_properties' );
	add_shortcode( 'sp_property_search', 'spr_shortcode_property_search' );
	add_shortcode( 'sp_enquiry_form', 'spr_shortcode_enquiry_form' );
	add_shortcode( 'sp_testimonials', 'spr_shortcode_testimonials' );
	add_shortcode( 'sp_business_info', 'spr_shortcode_business_info' );
}
add_action( 'init', 'spr_register_shortcodes' );

/**
 * Plugin fallback CSS — only when the active theme does NOT style our markup.
 * The SP Realtors theme declares add_theme_support( 'sp-realtors-core' ).
 */
function spr_maybe_enqueue_front_css() {
	if ( current_theme_supports( 'sp-realtors-core' ) ) {
		return;
	}
	wp_enqueue_style( 'spr-front', SPR_URL . 'assets/css/spr-front.css', array(), SPR_VERSION );
}

/**
 * Render a grid of properties from a query.
 *
 * @param WP_Query $query   Query.
 * @param int      $columns Columns (1–4).
 * @return string
 */
function spr_render_property_grid( $query, $columns = 3 ) {
	spr_maybe_enqueue_front_css();

	$columns = min( 4, max( 1, absint( $columns ) ) );

	if ( ! $query->have_posts() ) {
		return '<p class="spr-empty">' . esc_html__( 'No properties found right now. Please check back soon.', 'sp-realtors-core' ) . '</p>';
	}

	ob_start();
	echo '<div class="spr-grid spr-grid--cols-' . esc_attr( $columns ) . '">';
	while ( $query->have_posts() ) {
		$query->the_post();
		spr_get_template_part(
			'card-property',
			array(
				'post_id' => get_the_ID(),
				'heading' => 'h3',
			)
		);
	}
	echo '</div>';
	wp_reset_postdata();
	return ob_get_clean();
}

/**
 * [sp_featured_properties].
 *
 * @param array $atts Attributes.
 * @return string
 */
function spr_shortcode_featured_properties( $atts ) {
	$atts = shortcode_atts(
		array(
			'count'   => 3,
			'columns' => 3,
		),
		$atts,
		'sp_featured_properties'
	);
	return spr_render_property_grid( spr_featured_properties_query( min( 24, absint( $atts['count'] ) ) ), $atts['columns'] );
}

/**
 * [sp_properties].
 *
 * @param array $atts Attributes.
 * @return string
 */
function spr_shortcode_properties( $atts ) {
	$atts = shortcode_atts(
		array(
			'location' => '',
			'ptype'    => '',
			'purpose'  => '',
			'config'   => '',
			'budget'   => '',
			'sort'     => 'latest',
			'count'    => 6,
			'columns'  => 3,
		),
		$atts,
		'sp_properties'
	);

	$filters = spr_get_filter_values( $atts );
	$args    = array_merge(
		array(
			'post_type'           => 'property',
			'post_status'         => 'publish',
			'posts_per_page'      => min( 48, max( 1, absint( $atts['count'] ) ) ),
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
		),
		spr_build_property_query_args( $filters )
	);

	return spr_render_property_grid( new WP_Query( $args ), $atts['columns'] );
}

/**
 * [sp_property_search].
 *
 * @return string
 */
function spr_shortcode_property_search() {
	spr_maybe_enqueue_front_css();
	ob_start();
	spr_get_template_part( 'search-form', array( 'filters' => spr_get_filter_values() ) );
	return ob_get_clean();
}

/**
 * [sp_enquiry_form].
 *
 * @param array $atts Attributes.
 * @return string
 */
function spr_shortcode_enquiry_form( $atts ) {
	$atts = shortcode_atts(
		array(
			'property_id'  => 0,
			'title'        => '',
			'show_email'   => 'yes',
			'show_message' => 'yes',
			'button'       => '',
		),
		$atts,
		'sp_enquiry_form'
	);

	spr_maybe_enqueue_front_css();

	$property_id = absint( $atts['property_id'] );
	if ( ! $property_id && is_singular( 'property' ) ) {
		$property_id = get_the_ID();
	}

	$args = array(
		'property_id'  => $property_id,
		'source'       => $property_id ? 'property' : 'contact',
		'title'        => sanitize_text_field( $atts['title'] ),
		'show_email'   => 'no' !== $atts['show_email'],
		'show_message' => 'no' !== $atts['show_message'],
	);
	if ( '' !== $atts['button'] ) {
		$args['button_label'] = sanitize_text_field( $atts['button'] );
	}

	return spr_render_enquiry_form( $args );
}

/**
 * [sp_testimonials].
 *
 * @param array $atts Attributes.
 * @return string
 */
function spr_shortcode_testimonials( $atts ) {
	$atts = shortcode_atts( array( 'count' => 3 ), $atts, 'sp_testimonials' );

	spr_maybe_enqueue_front_css();

	$query = spr_testimonials_query( min( 24, absint( $atts['count'] ) ) );
	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	echo '<div class="spr-grid spr-grid--cols-3 spr-testimonials">';
	while ( $query->have_posts() ) {
		$query->the_post();
		spr_get_template_part( 'card-testimonial', array( 'post_id' => get_the_ID() ) );
	}
	echo '</div>';
	wp_reset_postdata();
	return ob_get_clean();
}

/**
 * [sp_business_info field="phone|whatsapp|email|address|hours" link="yes"].
 *
 * @param array $atts Attributes.
 * @return string
 */
function spr_shortcode_business_info( $atts ) {
	$atts  = shortcode_atts(
		array(
			'field' => 'phone',
			'link'  => 'yes',
		),
		$atts,
		'sp_business_info'
	);
	$field = sanitize_key( $atts['field'] );
	$value = spr_get_business( $field );

	if ( '' === $value ) {
		return '';
	}

	$link = 'no' !== $atts['link'];

	switch ( $field ) {
		case 'phone':
			return $link && spr_tel_url() ? '<a href="' . esc_url( spr_tel_url() ) . '">' . esc_html( $value ) . '</a>' : esc_html( $value );
		case 'whatsapp':
			return $link && spr_whatsapp_url() ? '<a href="' . esc_url( spr_whatsapp_url() ) . '" target="_blank" rel="noopener">' . esc_html( $value ) . '</a>' : esc_html( $value );
		case 'email':
			return $link && is_email( $value ) ? '<a href="' . esc_url( 'mailto:' . $value ) . '">' . esc_html( $value ) . '</a>' : esc_html( $value );
		case 'address':
		case 'hours':
			return nl2br( esc_html( $value ) );
		case 'facebook':
		case 'instagram':
		case 'youtube':
		case 'linkedin':
		case 'x':
			return '<a href="' . esc_url( $value ) . '" target="_blank" rel="noopener">' . esc_html( $value ) . '</a>';
		default:
			return esc_html( $value );
	}
}
