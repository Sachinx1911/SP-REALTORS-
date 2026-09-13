<?php
/**
 * Customizer section: Business Info (phone, WhatsApp, email, address, hours, social).
 *
 * WHY HERE (and why type "option"): Contact details are business data used by
 * the plugin itself (WhatsApp links, enquiry emails, schema). Stored as an
 * option — not a theme_mod — so they survive a theme switch. The theme only
 * adds selective-refresh partials for where it displays them.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Only digits (for wa.me links).
 *
 * @param string $value Raw.
 * @return string
 */
function spr_sanitize_digits( $value ) {
	return preg_replace( '/\D+/', '', (string) $value );
}

/**
 * Phone as typed, limited to phone characters.
 *
 * @param string $value Raw.
 * @return string
 */
function spr_sanitize_phone_display( $value ) {
	return trim( preg_replace( '/[^\d+\-\s()]/', '', sanitize_text_field( (string) $value ) ) );
}

/**
 * Register the Business Info section.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function spr_customize_business( $wp_customize ) {
	$wp_customize->add_section(
		'spr_business',
		array(
			'title'       => __( 'SP Realtors: Business Info', 'sp-realtors-core' ),
			'description' => __( 'Contact details used across the website, WhatsApp buttons, enquiry emails and Google structured data. Stored by the SP Realtors Core plugin, so they stay even if the theme changes.', 'sp-realtors-core' ),
			'priority'    => 25,
			'capability'  => 'manage_options',
		)
	);

	$fields = array(
		'name'      => array( __( 'Business name', 'sp-realtors-core' ), 'text', 'sanitize_text_field', '' ),
		'phone'     => array( __( 'Phone number', 'sp-realtors-core' ), 'tel', 'spr_sanitize_phone_display', __( 'Shown on the site, e.g. +91 98XXX XXXXX', 'sp-realtors-core' ) ),
		'whatsapp'  => array( __( 'WhatsApp number', 'sp-realtors-core' ), 'text', 'spr_sanitize_digits', __( 'Digits only with country code, e.g. 919800000000', 'sp-realtors-core' ) ),
		'email'     => array( __( 'Email', 'sp-realtors-core' ), 'email', 'sanitize_email', '' ),
		'address'   => array( __( 'Office address', 'sp-realtors-core' ), 'textarea', 'sanitize_textarea_field', '' ),
		'hours'     => array( __( 'Working hours', 'sp-realtors-core' ), 'text', 'sanitize_text_field', __( 'e.g. Mon – Sat: 10:00 AM – 7:00 PM', 'sp-realtors-core' ) ),
		'facebook'  => array( __( 'Facebook URL', 'sp-realtors-core' ), 'url', 'esc_url_raw', __( 'Leave empty to hide the icon.', 'sp-realtors-core' ) ),
		'instagram' => array( __( 'Instagram URL', 'sp-realtors-core' ), 'url', 'esc_url_raw', '' ),
		'youtube'   => array( __( 'YouTube URL', 'sp-realtors-core' ), 'url', 'esc_url_raw', '' ),
		'linkedin'  => array( __( 'LinkedIn URL', 'sp-realtors-core' ), 'url', 'esc_url_raw', '' ),
		'x'         => array( __( 'X (Twitter) URL', 'sp-realtors-core' ), 'url', 'esc_url_raw', '' ),
	);

	$defaults = spr_get_business();

	foreach ( $fields as $key => $field ) {
		$setting_id = 'spr_business[' . $key . ']';

		$wp_customize->add_setting(
			$setting_id,
			array(
				'type'              => 'option',
				'capability'        => 'manage_options',
				'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
				'sanitize_callback' => $field[2],
				'transport'         => 'refresh', // The theme switches this to postMessage + partials.
			)
		);

		$wp_customize->add_control(
			'spr_business_' . $key,
			array(
				'label'       => $field[0],
				'description' => $field[3],
				'section'     => 'spr_business',
				'settings'    => $setting_id,
				'type'        => $field[1],
			)
		);
	}
}
add_action( 'customize_register', 'spr_customize_business' );
