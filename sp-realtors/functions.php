<?php
/**
 * Theme bootstrap: constants + module loader.
 *
 * WHY HERE: This is presentation only. All property/testimonial/enquiry data
 * and logic lives in the "SP Realtors Core" plugin (theme-territory rule),
 * so switching themes never loses content.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

define( 'SP_REALTORS_VERSION', '1.0.0' );
define( 'SP_REALTORS_DIR', get_template_directory() );
define( 'SP_REALTORS_URI', get_template_directory_uri() );

/**
 * Theme files, loaded in dependency order.
 */
$sp_realtors_includes = array(
	'inc/plugin-notice.php',
	'inc/setup.php',
	'inc/template-tags.php',
	'inc/enqueue.php',
	'inc/customizer.php',
	'inc/woocommerce.php',
);

foreach ( $sp_realtors_includes as $sp_realtors_include ) {
	$sp_realtors_include_path = SP_REALTORS_DIR . '/' . $sp_realtors_include;
	if ( file_exists( $sp_realtors_include_path ) ) {
		require $sp_realtors_include_path;
	}
}
unset( $sp_realtors_includes, $sp_realtors_include, $sp_realtors_include_path );
