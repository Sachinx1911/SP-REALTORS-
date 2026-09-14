<?php
/**
 * Styles, scripts, fonts.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

/**
 * Front-end styles + scripts.
 */
function sp_realtors_scripts() {
	$css_path = SP_REALTORS_DIR . '/assets/css/main.css';
	$css_ver  = file_exists( $css_path ) ? (string) filemtime( $css_path ) : SP_REALTORS_VERSION;
	wp_enqueue_style( 'sp-realtors-main', SP_REALTORS_URI . '/assets/css/main.css', array(), $css_ver );

	sp_realtors_inline_brand_colors();

	$js_path = SP_REALTORS_DIR . '/assets/js/main.js';
	$js_ver  = file_exists( $js_path ) ? (string) filemtime( $js_path ) : SP_REALTORS_VERSION;
	wp_enqueue_script(
		'sp-realtors-main',
		SP_REALTORS_URI . '/assets/js/main.js',
		array(),
		$js_ver,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	$sp_data = array(
		'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
		'archiveUrl' => sp_realtors_core_active() ? spr_get_properties_url() : home_url( '/' ),
		'i18n'       => array(
			'loading' => __( 'Loading…', 'sp-realtors' ),
			'noMore'  => __( 'No more properties.', 'sp-realtors' ),
			'menu'    => __( 'Menu', 'sp-realtors' ),
			'close'   => __( 'Close', 'sp-realtors' ),
		),
	);

	if ( sp_realtors_core_active() && ( is_post_type_archive( 'property' ) || is_tax( spr_get_property_taxonomies() ) ) ) {
		$sp_data['loadMore'] = spr_get_load_more_config();
	}

	wp_localize_script( 'sp-realtors-main', 'sprData', $sp_data );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'sp_realtors_scripts' );

/**
 * Push the Customizer brand colors in as CSS custom properties.
 */
function sp_realtors_inline_brand_colors() {
	$colors = array(
		'--spr-navy'  => sp_realtors_theme_mod( 'color_navy' ),
		'--spr-blue'  => sp_realtors_theme_mod( 'color_blue' ),
		'--spr-green' => sp_realtors_theme_mod( 'color_green' ),
		'--spr-gold'  => sp_realtors_theme_mod( 'color_gold' ),
	);

	$css = ':root{';
	foreach ( $colors as $property => $value ) {
		$value = sanitize_hex_color( $value );
		if ( $value ) {
			$css .= $property . ':' . $value . ';';
		}
	}
	$css .= '}';

	wp_add_inline_style( 'sp-realtors-main', $css );
}

/**
 * Only load per-block core styles that are actually used on the page.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/**
 * Preload the hero heading font, but only once real font files are shipped
 * in assets/fonts/ (until then the theme uses the system font stack).
 *
 * @param array $urls           Resource hint URLs.
 * @param string $relation_type Relation type.
 * @return array
 */
function sp_realtors_resource_hints( $urls, $relation_type ) {
	if ( 'preload' !== $relation_type ) {
		return $urls;
	}
	$font = SP_REALTORS_DIR . '/assets/fonts/playfair-display-700.woff2';
	if ( file_exists( $font ) ) {
		$urls[] = array(
			'href'        => SP_REALTORS_URI . '/assets/fonts/playfair-display-700.woff2',
			'as'          => 'font',
			'type'        => 'font/woff2',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'sp_realtors_resource_hints', 10, 2 );
