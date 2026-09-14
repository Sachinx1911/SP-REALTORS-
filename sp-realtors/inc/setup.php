<?php
/**
 * Theme supports, nav menus, sidebars, image sizes.
 *
 * WHY HERE: Presentation capabilities (Design → Theme rule).
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

/**
 * Core theme setup.
 */
function sp_realtors_setup() {
	load_theme_textdomain( 'sp-realtors', SP_REALTORS_DIR . '/languages' );

	// Tell the plugin our theme styles property/testimonial markup itself
	// (skips the plugin's front-end fallback CSS).
	add_theme_support( 'sp-realtors-core' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 60,
			'width'       => 200,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );

	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor-style.css' );

	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'Navy', 'sp-realtors' ),
				'slug'  => 'navy',
				'color' => '#092B50',
			),
			array(
				'name'  => __( 'Blue', 'sp-realtors' ),
				'slug'  => 'blue',
				'color' => '#12579A',
			),
			array(
				'name'  => __( 'Green', 'sp-realtors' ),
				'slug'  => 'green',
				'color' => '#12B95A',
			),
			array(
				'name'  => __( 'Gold', 'sp-realtors' ),
				'slug'  => 'gold',
				'color' => '#C89A45',
			),
			array(
				'name'  => __( 'Off-white', 'sp-realtors' ),
				'slug'  => 'off-white',
				'color' => '#F5F8FA',
			),
			array(
				'name'  => __( 'Charcoal', 'sp-realtors' ),
				'slug'  => 'charcoal',
				'color' => '#1F2933',
			),
			array(
				'name'  => __( 'Muted', 'sp-realtors' ),
				'slug'  => 'muted',
				'color' => '#5B6B7F',
			),
		)
	);

	add_theme_support(
		'editor-font-sizes',
		array(
			array(
				'name' => __( 'Small', 'sp-realtors' ),
				'slug' => 'small',
				'size' => 15,
			),
			array(
				'name' => __( 'Normal', 'sp-realtors' ),
				'slug' => 'normal',
				'size' => 17,
			),
			array(
				'name' => __( 'Large', 'sp-realtors' ),
				'slug' => 'large',
				'size' => 24,
			),
			array(
				'name' => __( 'Extra Large', 'sp-realtors' ),
				'slug' => 'x-large',
				'size' => 36,
			),
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'sp-realtors' ),
			'footer'  => __( 'Footer Menu', 'sp-realtors' ),
		)
	);

	add_image_size( 'spr-card', 640, 440, true );
	add_image_size( 'spr-gallery', 1200, 800, true );
	add_image_size( 'spr-thumb', 240, 160, true );
	add_image_size( 'spr-hero', 1920, 900, true );
	add_image_size( 'spr-avatar', 120, 120, true );
}
add_action( 'after_setup_theme', 'sp_realtors_setup' );

/**
 * Set the global content width (used by wp_get_attachment_image et al.).
 */
function sp_realtors_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'sp_realtors_content_width', 1200 );
}
add_action( 'after_setup_theme', 'sp_realtors_content_width', 0 );

/**
 * Register widget areas.
 */
function sp_realtors_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Footer Column 1', 'sp-realtors' ),
			'id'            => 'footer-1',
			'description'   => __( 'Widgets shown in the first footer column.', 'sp-realtors' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => __( 'Footer Column 2', 'sp-realtors' ),
			'id'            => 'footer-2',
			'description'   => __( 'Widgets shown in the second footer column.', 'sp-realtors' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'sp-realtors' ),
			'id'            => 'blog-sidebar',
			'description'   => __( 'Widgets shown next to blog posts and pages.', 'sp-realtors' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'sp_realtors_widgets_init' );

/**
 * Shorter amenity labels for the design (plugin owns the master list;
 * this only relabels two entries via the filter it already provides).
 *
 * @param array $labels Amenity key => label.
 * @return array
 */
function sp_realtors_amenity_labels( $labels ) {
	if ( isset( $labels['security'] ) ) {
		$labels['security'] = __( 'Security', 'sp-realtors' );
	}
	if ( isset( $labels['parking'] ) ) {
		$labels['parking'] = __( 'Car Parking', 'sp-realtors' );
	}
	return $labels;
}
add_filter( 'spr_amenities_list', 'sp_realtors_amenity_labels' );
