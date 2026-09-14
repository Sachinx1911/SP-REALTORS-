<?php
/**
 * WooCommerce support (guarded — loads nothing when WooCommerce is inactive).
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Declare WooCommerce theme support.
 */
function sp_realtors_woocommerce_setup() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'sp_realtors_woocommerce_setup' );

/**
 * Wrap WooCommerce content in the theme's page container.
 */
function sp_realtors_woocommerce_wrapper_start() {
	echo '<main id="primary" class="site-main woocommerce-page">';
}
add_action( 'woocommerce_before_main_content', 'sp_realtors_woocommerce_wrapper_start' );

/**
 * Close the theme page container.
 */
function sp_realtors_woocommerce_wrapper_end() {
	echo '</main>';
}
add_action( 'woocommerce_after_main_content', 'sp_realtors_woocommerce_wrapper_end' );
