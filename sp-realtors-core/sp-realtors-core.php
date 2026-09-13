<?php
/**
 * Plugin Name:       SP Realtors Core
 * Plugin URI:        https://sprealtors.in
 * Description:       Properties, locations, testimonials and enquiries for SP REALTORS. All listing data lives here (not in the theme), so nothing is lost if the theme is ever changed.
 * Version:           1.0.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            SP REALTORS
 * Author URI:        https://sprealtors.in
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sp-realtors-core
 * Domain Path:       /languages
 *
 * @package SP_Realtors_Core
 */

/*
 * WHY A PLUGIN?
 * The WordPress Theme Handbook says themes must only handle presentation
 * ("plugin territory" rule). Custom post types, taxonomies, meta, forms and
 * structured data are *content/functionality*, so they live in this plugin.
 * If the broker switches theme, every property, location and enquiry stays.
 */

defined( 'ABSPATH' ) || exit;

define( 'SPR_VERSION', '1.0.0' );
define( 'SPR_FILE', __FILE__ );
define( 'SPR_PATH', plugin_dir_path( __FILE__ ) );
define( 'SPR_URL', plugin_dir_url( __FILE__ ) );
define( 'SPR_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Plugin files, loaded in dependency order.
 * helpers.php first: it defines the public API used by everything else (and by the theme).
 */
$spr_includes = array(
	'includes/helpers.php',
	'includes/cpt-property.php',
	'includes/cpt-testimonial.php',
	'includes/cpt-enquiry.php',
	'includes/taxonomies.php',
	'includes/term-meta.php',
	'includes/meta-register.php',
	'includes/meta-boxes.php',
	'includes/meta-save.php',
	'includes/admin-columns.php',
	'includes/admin-gallery.php',
	'includes/activation.php',
);

foreach ( $spr_includes as $spr_file ) {
	require_once SPR_PATH . $spr_file;
}
unset( $spr_includes, $spr_file );

register_activation_hook( SPR_FILE, 'spr_activate' );
register_deactivation_hook( SPR_FILE, 'spr_deactivate' );

/**
 * Load translations.
 *
 * Hooked to `init` (not `plugins_loaded`) as required since WordPress 6.7.
 */
function spr_load_textdomain() {
	load_plugin_textdomain( 'sp-realtors-core', false, dirname( SPR_BASENAME ) . '/languages' );
}
add_action( 'init', 'spr_load_textdomain', 0 );

/**
 * "Settings" shortcut on the Plugins screen.
 *
 * @param array $links Existing action links.
 * @return array
 */
function spr_plugin_action_links( $links ) {
	$url = admin_url( 'edit.php?post_type=property' );
	array_unshift(
		$links,
		sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html__( 'Properties', 'sp-realtors-core' ) )
	);
	return $links;
}
add_filter( 'plugin_action_links_' . SPR_BASENAME, 'spr_plugin_action_links' );
