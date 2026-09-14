<?php
/**
 * Reusable template tags: theme_mod defaults, icons, breadcrumbs, menus, social links.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Theme mod defaults (design content — shared by inc/customizer.php).
 * ---------------------------------------------------------------------- */

/**
 * Default value for every `spr_theme_*` theme mod.
 *
 * Keeping one map means the Customizer registration and the front-end
 * getter can never drift out of sync.
 *
 * @return array key => default value
 */
function sp_realtors_theme_mod_defaults() {
	$defaults = array(
		'hero_eyebrow'      => __( 'A Better Tomorrow Starts Here', 'sp-realtors' ),
		'hero_heading'      => __( 'Find Your Perfect Property', 'sp-realtors' ),
		'hero_subtext'      => __( 'Residential & Commercial Properties in Navi Mumbai and surrounding areas.', 'sp-realtors' ),
		'hero_image'        => 0,

		'cta_heading'       => __( 'Looking for a Property?', 'sp-realtors' ),
		'cta_text'          => __( "Tell us your requirement and we'll help you find the right option.", 'sp-realtors' ),
		'cta_button_label'  => __( 'WhatsApp Us', 'sp-realtors' ),

		'footer_tagline'    => __( 'Properties · People · Possibilities', 'sp-realtors' ),
		'footer_copyright'  => __( 'SP REALTORS. All rights reserved.', 'sp-realtors' ),

		'color_navy'        => '#092B50',
		'color_blue'        => '#12579A',
		'color_green'       => '#12B95A',
		'color_gold'        => '#C89A45',

		'mobile_sticky_bar' => true,

		'contact_form_shortcode' => '',
		'contact_map_url'        => '',
	);

	$trust_defaults = array(
		array( 'icon' => 'check', 'title' => __( 'Verified Properties', 'sp-realtors' ), 'text' => __( 'Genuine listings', 'sp-realtors' ) ),
		array( 'icon' => 'users', 'title' => __( 'Local Expertise', 'sp-realtors' ), 'text' => __( 'In-depth knowledge', 'sp-realtors' ) ),
		array( 'icon' => 'shield', 'title' => __( 'Transparent Deals', 'sp-realtors' ), 'text' => __( 'Clear and honest guidance', 'sp-realtors' ) ),
		array( 'icon' => 'headset', 'title' => __( 'Complete Assistance', 'sp-realtors' ), 'text' => __( 'From search to final deal', 'sp-realtors' ) ),
	);
	foreach ( $trust_defaults as $i => $item ) {
		$n                                     = $i + 1;
		$defaults[ "trust_{$n}_icon" ]  = $item['icon'];
		$defaults[ "trust_{$n}_title" ] = $item['title'];
		$defaults[ "trust_{$n}_text" ]  = $item['text'];
	}

	$requirement_defaults = array(
		array( 'title' => __( 'Buy Property', 'sp-realtors' ), 'text' => __( 'Find your dream home', 'sp-realtors' ) ),
		array( 'title' => __( 'Rent Property', 'sp-realtors' ), 'text' => __( 'Homes & offices for rent', 'sp-realtors' ) ),
		array( 'title' => __( 'Residential', 'sp-realtors' ), 'text' => __( 'Apartments, Villas & More', 'sp-realtors' ) ),
		array( 'title' => __( 'Commercial', 'sp-realtors' ), 'text' => __( 'Office, Shop, Showroom & More', 'sp-realtors' ) ),
	);
	$requirement_urls = array( '', '', '', '' );
	if ( sp_realtors_core_active() ) {
		$requirement_urls = array(
			spr_get_filtered_properties_url( array( 'purpose' => 'buy' ) ),
			spr_get_filtered_properties_url( array( 'purpose' => 'rent' ) ),
			spr_get_properties_url(),
			spr_get_properties_url(),
		);
	}
	foreach ( $requirement_defaults as $i => $item ) {
		$n                                     = $i + 1;
		$defaults[ "requirement_{$n}_title" ] = $item['title'];
		$defaults[ "requirement_{$n}_text" ]  = $item['text'];
		$defaults[ "requirement_{$n}_image" ] = 0;
		$defaults[ "requirement_{$n}_url" ]   = $requirement_urls[ $i ];
	}

	$why_defaults = array(
		array( 'icon' => 'check', 'title' => __( 'Verified Properties', 'sp-realtors' ), 'text' => __( 'Genuine listings', 'sp-realtors' ) ),
		array( 'icon' => 'users', 'title' => __( 'Local Market Expertise', 'sp-realtors' ), 'text' => __( 'In-depth knowledge', 'sp-realtors' ) ),
		array( 'icon' => 'shield', 'title' => __( 'Transparent Deals', 'sp-realtors' ), 'text' => __( 'Clear and honest guidance', 'sp-realtors' ) ),
		array( 'icon' => 'headset', 'title' => __( 'Complete Assistance', 'sp-realtors' ), 'text' => __( 'From search to final deal', 'sp-realtors' ) ),
		array( 'icon' => '', 'title' => '', 'text' => '' ),
	);
	foreach ( $why_defaults as $i => $item ) {
		$n                                 = $i + 1;
		$defaults[ "why_{$n}_icon" ] = $item['icon'];
		$defaults[ "why_{$n}_title" ] = $item['title'];
		$defaults[ "why_{$n}_text" ] = $item['text'];
	}

	$stat_defaults = array(
		array( 'number' => '100+', 'label' => __( 'Happy Clients', 'sp-realtors' ) ),
		array( 'number' => '50+', 'label' => __( 'Properties Sold', 'sp-realtors' ) ),
		array( 'number' => '5+', 'label' => __( 'Years of Experience', 'sp-realtors' ) ),
		array( 'number' => __( 'Navi Mumbai', 'sp-realtors' ), 'label' => __( 'Our Focus Region', 'sp-realtors' ) ),
	);
	foreach ( $stat_defaults as $i => $item ) {
		$n                                     = $i + 1;
		$defaults[ "stat_{$n}_number" ] = $item['number'];
		$defaults[ "stat_{$n}_label" ]  = $item['label'];
	}

	$approach_defaults = array(
		array( 'title' => __( 'Understand Your Needs', 'sp-realtors' ), 'text' => '' ),
		array( 'title' => __( 'Share Best Options', 'sp-realtors' ), 'text' => '' ),
		array( 'title' => __( 'Site Visits & Comparisons', 'sp-realtors' ), 'text' => '' ),
		array( 'title' => __( 'Support Till Final Deal', 'sp-realtors' ), 'text' => '' ),
	);
	foreach ( $approach_defaults as $i => $item ) {
		$n                                          = $i + 1;
		$defaults[ "approach_{$n}_title" ] = $item['title'];
		$defaults[ "approach_{$n}_text" ]  = $item['text'];
	}

	return apply_filters( 'sp_realtors_theme_mod_defaults', $defaults );
}

/**
 * Get an `spr_theme_{$key}` theme mod with its default applied.
 *
 * @param string $key Key without the `spr_theme_` prefix.
 * @return mixed
 */
function sp_realtors_theme_mod( $key ) {
	$defaults = sp_realtors_theme_mod_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return get_theme_mod( 'spr_theme_' . $key, $default );
}

/* -------------------------------------------------------------------------
 * Icons.
 * ---------------------------------------------------------------------- */

/**
 * Icon choices offered in the Customizer (trust strip / why choose us).
 *
 * @return array key => label
 */
function sp_realtors_icon_choices() {
	return array(
		'check'   => __( 'Check', 'sp-realtors' ),
		'shield'  => __( 'Shield', 'sp-realtors' ),
		'award'   => __( 'Award', 'sp-realtors' ),
		'users'   => __( 'Users', 'sp-realtors' ),
		'clock'   => __( 'Clock', 'sp-realtors' ),
		'home'    => __( 'Home', 'sp-realtors' ),
		'key'     => __( 'Key', 'sp-realtors' ),
		'star'    => __( 'Star', 'sp-realtors' ),
		'headset' => __( 'Headset (support)', 'sp-realtors' ),
	);
}

/**
 * Inline SVG for a small, fixed icon set (menu, contact, social, trust/why).
 *
 * Output is a hardcoded allow-list, so it is safe to echo unescaped.
 *
 * @param string $name Icon name.
 * @return string SVG markup or '' if unknown.
 */
function sp_realtors_icon( $name ) {
	$icons = array(
		'menu'        => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M3 6h18v2H3zm0 5h18v2H3zm0 5h18v2H3z"/></svg>',
		'close'       => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M6.4 5 5 6.4 10.6 12 5 17.6 6.4 19l5.6-5.6 5.6 5.6 1.4-1.4-5.6-5.6L19 6.4 17.6 5 12 10.6z"/></svg>',
		'phone'       => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="currentColor" d="M6.6 10.8c1.4 2.7 3.6 4.9 6.3 6.3l2.1-2.1c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.5.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.4 21 3 13.6 3 4.5c0-.6.4-1 1-1H7.6c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.5.1.4 0 .8-.2 1z"/></svg>',
		'whatsapp'    => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15L2 22l5.1-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20zm4.4-5.9c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1-.2.2-.6.8-.8 1-.1.2-.3.2-.5.1-.2-.1-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.3-1.6-.1-.2 0-.4.1-.5l.4-.4c.1-.1.2-.3.2-.4.1-.1 0-.3 0-.4l-.7-1.6c-.2-.4-.4-.4-.5-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 1.9s.8 2.2 1 2.4c.1.1 1.6 2.5 4 3.4.6.2 1 .4 1.3.5.6.2 1.1.1 1.5-.1.5-.1 1.4-.6 1.6-1.1.2-.5.2-.9.1-1z"/></svg>',
		'email'       => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="currentColor" d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zm16 4-8 5-8-5V6l8 5 8-5z"/></svg>',
		'location'    => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>',
		'chevron-right' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M9 6l6 6-6 6"/></svg>',
		'chevron-left'  => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M15 6l-6 6 6 6"/></svg>',
		'star'        => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2l2.9 6.3 6.9.7-5.2 4.7 1.5 6.8L12 17.6 5.9 20.5l1.5-6.8-5.2-4.7 6.9-.7z"/></svg>',
		'check'       => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>',
		'shield'      => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5z"/></svg>',
		'award'       => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2a5 5 0 1 1 0 10 5 5 0 0 1 0-10zM8.2 12.9 6 21l6-2 6 2-2.2-8.1"/></svg>',
		'users'       => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M16 11a4 4 0 1 0-4-4 4 4 0 0 0 4 4zm-8 0a3 3 0 1 0-3-3 3 3 0 0 0 3 3zm0 2c-2.3 0-7 1.2-7 3.5V19h9v-2.5c0-.9.3-2 1-2.9C10.2 13.1 8.7 13 8 13zm8 0c-.7 0-2.5.2-3.9 1-1 .5-2.1 1.3-2.1 2.5V19h12v-2.5C22 14.2 18.3 13 16 13z"/></svg>',
		'clock'       => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 10.6 4.2 2.5-.8 1.3-5.4-3.2V6h1.6z"/></svg>',
		'home'        => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 3 2 12h3v8h6v-6h2v6h6v-8h3z"/></svg>',
		'key'         => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M14.5 2a5.5 5.5 0 0 0-5.4 6.6L2 15.7V20h4.3l1.1-1.1v-2h2v-2h2l1.9-1.9A5.5 5.5 0 1 0 14.5 2zm2 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3z"/></svg>',
		'headset'     => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2a8 8 0 0 0-8 8v6a3 3 0 0 0 3 3h1v-7H5v-2a7 7 0 0 1 14 0v2h-3v7h1a3 3 0 0 0 3-3v-1a1 1 0 0 0 0-2v-3a8 8 0 0 0-8-8zm-4 13h1v5H8a2 2 0 0 1-2-2v-3h2zm8 0h2v3a2 2 0 0 1-2 2h-1v-5z"/></svg>',
		'search'      => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M10 2a8 8 0 1 0 4.9 14.3l5.4 5.4 1.4-1.4-5.4-5.4A8 8 0 0 0 10 2zm0 2a6 6 0 1 1 0 12 6 6 0 0 1 0-12z"/></svg>',
		'grid'        => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M4 4h7v7H4zm9 0h7v7h-7zM4 13h7v7H4zm9 0h7v7h-7z"/></svg>',
		'compare'     => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M9 3H5a2 2 0 0 0-2 2v4h2V5h4zm10 2a2 2 0 0 0-2-2h-4v2h4v4h2zM5 15H3v4a2 2 0 0 0 2 2h4v-2H5zm14 4v-4h-2v4h-4v2h4a2 2 0 0 0 2-2zM7 8h10v8H7z"/></svg>',
		'lift'        => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M4 2h16v20H4zm4 4 3-3 3 3H8zm0 6 3 3 3-3H8z"/></svg>',
		'bolt'        => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M13 2 4 14h6l-1 8 9-12h-6z"/></svg>',
		'car'         => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M5 11 6.5 6h11L19 11m-1.5 5.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm-11 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zM19 11H5a2 2 0 0 0-2 2v5h2v2h2v-2h10v2h2v-2h2v-5a2 2 0 0 0-2-2z"/></svg>',
		'dumbbell'    => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M2 10h2v4H2zm3-2h2v8H5zm3 3h8v2H8zm9-3h2v8h-2zm3 2h2v4h-2z"/></svg>',
		'child'       => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM6 9h12l-2 4h-2v9h-2v-6h-2v6H8v-9H8z"/></svg>',
		'pool'        => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M3 17c1.2 0 1.8.6 3 .6s1.8-.6 3-.6 1.8.6 3 .6 1.8-.6 3-.6 1.8.6 3 .6 1.8-.6 3-.6v2c-1.2 0-1.8.6-3 .6s-1.8-.6-3-.6-1.8.6-3 .6-1.8-.6-3-.6-1.8.6-3 .6-1.8-.6-3-.6zm5-4L4 9l1.4-1.4L9 11l7-7 1.4 1.4z"/></svg>',
		'garden'      => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2c3 2 4 5 4 7a4 4 0 0 1-3 3.9V21h-2v-8.1A4 4 0 0 1 8 9c0-2 1-5 4-7z"/></svg>',
		'building'    => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M4 21V4h9v6h7v11h-7v-4h-2v4zm2-13h2V6H6zm0 4h2v-2H6zm0 4h2v-2H6zm5-4h2V8h-2zm0 4h2v-2h-2zm6 2h2v-2h-2zm0 4h2v-2h-2z"/></svg>',
		'cctv'        => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M2 4h14v2H4v3h9v2H8l9 3v6h-2v-4.6L4 12v6H2z"/></svg>',
		'flame'       => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2s5 4.5 5 9.5A5 5 0 0 1 7 11.5c0-.7.1-1.3.3-1.9C6.5 11 6 12.7 6 14a6 6 0 0 0 12 0c0-6-6-12-6-12z"/></svg>',
		'intercom'    => '<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M6.6 10.8c1.4 2.7 3.6 4.9 6.3 6.3l2.1-2.1c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.5.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.4 21 3 13.6 3 4.5c0-.6.4-1 1-1H7.6c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.5.1.4 0 .8-.2 1z"/></svg>',
		'store'       => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M3 4h18l1 5a3 3 0 0 1-2 2.8V21h-2v-9h-4v9H6v-9H4v-4a3 3 0 0 1-2-2.8zm2 7a1 1 0 0 0 1-1V7h1v3a1 1 0 0 0 2 0V7h2v3a1 1 0 0 0 2 0V7h2v3a1 1 0 0 0 2 0V7h1v3a1 1 0 0 0 1 1 1 1 0 0 0 1-1.1L19 6H5l-.9 3.9A1 1 0 0 0 5 11z"/></svg>',
		'area'        => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M4 4h6v2H6v4H4zm10 0h6v6h-2V6h-4zM4 14h2v4h4v2H4zm16 0v6h-6v-2h4v-4z"/></svg>',
		'bed'         => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M3 7h2v6h6V9a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v6h1v4h-2v-2H4v2H2v-9h1zm5 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>',
		'bath'        => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 4a2 2 0 0 1 2 2v3H4v7a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2h1v-2H8V6a1 1 0 0 0-2 0H4a3 3 0 0 1 3-3z"/></svg>',
		'quote'       => '<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M9 7C6.2 7 4 9.2 4 12s2.2 5 5 5v-3c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2v-3c0-1.1-.9-2-2-2zm10 0c-2.8 0-5 2.2-5 5s2.2 5 5 5v-3c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2v-3c0-1.1-.9-2-2-2z"/></svg>',
		'facebook'    => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="currentColor" d="M14 9h3V6h-3c-1.7 0-3 1.3-3 3v2H9v3h2v7h3v-7h2.6l.4-3H14V9c0-.3.2-.5.5-.5z"/></svg>',
		'instagram'   => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="currentColor" d="M8 3h8a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5V8a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3zm4 3a4 4 0 1 1 0 8 4 4 0 0 1 0-8zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm4.5-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>',
		'youtube'     => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="currentColor" d="M22 12c0-2-.2-3.3-.4-4a2.5 2.5 0 0 0-1.8-1.8C18 6 12 6 12 6s-6 0-7.8.2A2.5 2.5 0 0 0 2.4 8C2.2 8.7 2 10 2 12s.2 3.3.4 4A2.5 2.5 0 0 0 4.2 17.8C6 18 12 18 12 18s6 0 7.8-.2A2.5 2.5 0 0 0 21.6 16c.2-.7.4-2 .4-4zM10 15V9l5 3z"/></svg>',
		'linkedin'    => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="currentColor" d="M4.98 3.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM3 9h4v12H3zM9 9h3.8v1.7h.1c.5-1 1.8-2 3.7-2 4 0 4.7 2.6 4.7 6V21h-4v-5.3c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9V21H9z"/></svg>',
		'x'           => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="currentColor" d="M3 3h4.6l4 5.5L16.4 3H21l-6.8 8.8L21 21h-4.6l-4.4-6-5.5 6H2l7.2-9.5z"/></svg>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/**
 * Icon name for an amenity key (see spr_get_amenities_list()).
 *
 * @param string $amenity_key Amenity key.
 * @return string Icon name for sp_realtors_icon().
 */
function sp_realtors_amenity_icon( $amenity_key ) {
	$map = array(
		'lift'         => 'lift',
		'parking'      => 'car',
		'gym'          => 'dumbbell',
		'pool'         => 'pool',
		'security'     => 'shield',
		'power-backup' => 'bolt',
		'garden'       => 'garden',
		'clubhouse'    => 'building',
		'play-area'    => 'child',
		'cctv'         => 'cctv',
		'gas-pipeline' => 'flame',
		'intercom'     => 'intercom',
	);
	return isset( $map[ $amenity_key ] ) ? $map[ $amenity_key ] : 'check';
}

/* -------------------------------------------------------------------------
 * Menus.
 * ---------------------------------------------------------------------- */

/**
 * Simple "Home" link used when the `primary` menu location has no menu assigned.
 */
function sp_realtors_menu_fallback() {
	echo '<ul id="primary-menu" class="sp-realtors-nav__list">';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'sp-realtors' ) . '</a></li>';
	if ( sp_realtors_core_active() ) {
		echo '<li class="menu-item"><a href="' . esc_url( spr_get_properties_url() ) . '">' . esc_html__( 'Properties', 'sp-realtors' ) . '</a></li>';
	}
	echo '</ul>';
}

/* -------------------------------------------------------------------------
 * Social links (business info comes from the plugin option, survives theme switch).
 * ---------------------------------------------------------------------- */

/**
 * Output social icon links, skipping any that are empty.
 */
function sp_realtors_social_links() {
	if ( ! sp_realtors_core_active() ) {
		return;
	}

	$networks = array(
		'facebook'  => __( 'Facebook', 'sp-realtors' ),
		'instagram' => __( 'Instagram', 'sp-realtors' ),
		'youtube'   => __( 'YouTube', 'sp-realtors' ),
		'linkedin'  => __( 'LinkedIn', 'sp-realtors' ),
		'x'         => __( 'X (Twitter)', 'sp-realtors' ),
	);

	$links = array();
	foreach ( $networks as $key => $label ) {
		$url = spr_get_business( $key );
		if ( '' !== $url ) {
			$links[ $key ] = array(
				'url'   => $url,
				'label' => $label,
			);
		}
	}

	if ( ! $links ) {
		return;
	}

	echo '<ul class="sp-realtors-social">';
	foreach ( $links as $key => $link ) {
		printf(
			'<li><a href="%1$s" target="_blank" rel="noopener noreferrer"><span class="screen-reader-text">%2$s</span>%3$s</a></li>',
			esc_url( $link['url'] ),
			esc_html( $link['label'] ),
			sp_realtors_icon( $key ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed allow-listed SVG.
		);
	}
	echo '</ul>';
}

/* -------------------------------------------------------------------------
 * Breadcrumbs.
 * ---------------------------------------------------------------------- */

/**
 * Breadcrumb trail. Defers to Yoast / Rank Math if active; otherwise a
 * minimal built-in trail with BreadcrumbList structured data.
 */
function sp_realtors_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<nav class="sp-realtors-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'sp-realtors' ) . '">', '</nav>' );
		return;
	}
	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		rank_math_the_breadcrumbs();
		return;
	}

	$trail = array( array( __( 'Home', 'sp-realtors' ), home_url( '/' ) ) );

	if ( sp_realtors_core_active() && ( is_post_type_archive( 'property' ) || is_singular( 'property' ) || is_tax( spr_get_property_taxonomies() ) ) ) {
		$trail[] = array( __( 'Properties', 'sp-realtors' ), spr_get_properties_url() );

		if ( is_tax() ) {
			$term    = get_queried_object();
			$trail[] = array( $term->name, get_term_link( $term ) );
		} elseif ( is_singular( 'property' ) ) {
			$location = spr_get_first_term( get_the_ID(), 'property_location' );
			if ( $location ) {
				$trail[] = array( $location->name, get_term_link( $location ) );
			}
			$trail[] = array( get_the_title(), '' );
		}
	} elseif ( is_singular() ) {
		$trail[] = array( get_the_title(), '' );
	} elseif ( is_search() ) {
		/* translators: %s: search query */
		$trail[] = array( sprintf( __( 'Search results for “%s”', 'sp-realtors' ), get_search_query() ), '' );
	} elseif ( is_404() ) {
		$trail[] = array( __( 'Page not found', 'sp-realtors' ), '' );
	} elseif ( is_archive() ) {
		$trail[] = array( get_the_archive_title(), '' );
	}

	echo '<nav class="sp-realtors-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'sp-realtors' ) . '"><ol>';
	$list_items = array();
	foreach ( $trail as $i => $crumb ) {
		list( $label, $url ) = $crumb;
		$is_last              = ( count( $trail ) - 1 === $i );
		$list_items[]         = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => sp_realtors_core_active() ? spr_plain_text( $label ) : wp_strip_all_tags( $label ),
		) + ( $url ? array( 'item' => $url ) : array() );

		if ( $url && ! $is_last ) {
			printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $url ), esc_html( $label ) );
		} else {
			printf( '<li aria-current="page">%s</li>', esc_html( $label ) );
		}
	}
	echo '</ol></nav>';

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list_items,
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/* -------------------------------------------------------------------------
 * Blog post meta.
 * ---------------------------------------------------------------------- */

/**
 * Posted-on date + author for standard blog posts.
 */
function sp_realtors_posted_on() {
	printf(
		'<span class="sp-realtors-posted-on"><time class="entry-date published" datetime="%1$s">%2$s</time></span> <span class="sp-realtors-byline">%3$s <span class="author vcard">%4$s</span></span>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() ),
		esc_html__( 'by', 'sp-realtors' ),
		esc_html( get_the_author() )
	);
}

/* -------------------------------------------------------------------------
 * Misc.
 * ---------------------------------------------------------------------- */

/**
 * Label for a property status key (thin wrapper so templates never need
 * to know the plugin's option shape).
 *
 * @param string $status Status key.
 * @return string
 */
function sp_realtors_status_label( $status ) {
	if ( ! sp_realtors_core_active() ) {
		return '';
	}
	$options = spr_get_status_options();
	return isset( $options[ $status ] ) ? $options[ $status ] : '';
}
