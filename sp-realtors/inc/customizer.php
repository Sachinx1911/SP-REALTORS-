<?php
/**
 * "SP Realtors Theme" Customizer panel — design content (theme_mods).
 *
 * WHY THEME_MODS (not options): This is presentation content tied to this
 * theme's design (hero copy, section text, brand colors). If the theme is
 * ever replaced, a new design naturally needs new copy — unlike business
 * contact info (phone/email/social), which is plugin-owned so it survives
 * a theme switch. See inc/customizer-business.php note in the plugin.
 *
 * NOTE on live preview: full postMessage + selective-refresh partials are
 * wired up per section as its front-end markup is built (Phase 5 templates).
 * Until a section has a template, its controls use 'refresh' transport,
 * which is always correct, just not instant.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the panel, sections, settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function sp_realtors_customize_register( $wp_customize ) {
	$defaults = sp_realtors_theme_mod_defaults();

	$wp_customize->add_panel(
		'sp_realtors_theme',
		array(
			'title'    => __( 'SP Realtors Theme', 'sp-realtors' ),
			'priority' => 30,
		)
	);

	/**
	 * Helper: register one theme_mod setting + control.
	 *
	 * @param string $key       Key without the `spr_theme_` prefix.
	 * @param string $section   Section ID.
	 * @param string $label     Control label.
	 * @param string $type      Control type (text, textarea, url, image, checkbox, select).
	 * @param string $sanitize  Sanitize callback.
	 * @param array  $extra     Extra control args (e.g. 'choices', 'description').
	 */
	$add_field = function ( $key, $section, $label, $type, $sanitize, $extra = array() ) use ( $wp_customize, $defaults ) {
		$setting_id = 'spr_theme_' . $key;

		$wp_customize->add_setting(
			$setting_id,
			array(
				'type'              => 'theme_mod',
				'capability'        => 'edit_theme_options',
				'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
				'sanitize_callback' => $sanitize,
				'transport'         => in_array( $type, array( 'text', 'textarea' ), true ) ? 'postMessage' : 'refresh',
			)
		);

		$control_args = array_merge(
			array(
				'label'   => $label,
				'section' => $section,
				'type'    => $type,
			),
			$extra
		);

		if ( 'image' === $type ) {
			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $setting_id, array_merge( $control_args, array( 'settings' => $setting_id ) ) ) );
		} else {
			$wp_customize->add_control( $setting_id, $control_args );
		}
	};

	/* --------------------------------------------------------------
	 * Hero.
	 * ----------------------------------------------------------- */
	$wp_customize->add_section(
		'spr_theme_hero',
		array( 'title' => __( 'Hero', 'sp-realtors' ), 'panel' => 'sp_realtors_theme' )
	);
	$add_field( 'hero_eyebrow', 'spr_theme_hero', __( 'Eyebrow text', 'sp-realtors' ), 'text', 'sanitize_text_field' );
	$add_field( 'hero_heading', 'spr_theme_hero', __( 'Heading', 'sp-realtors' ), 'text', 'sanitize_text_field' );
	$add_field( 'hero_subtext', 'spr_theme_hero', __( 'Subtext', 'sp-realtors' ), 'textarea', 'sanitize_textarea_field' );
	$add_field( 'hero_image', 'spr_theme_hero', __( 'Background image', 'sp-realtors' ), 'image', 'absint' );

	/* --------------------------------------------------------------
	 * Trust Strip (4 items).
	 * ----------------------------------------------------------- */
	$wp_customize->add_section(
		'spr_theme_trust',
		array( 'title' => __( 'Trust Strip', 'sp-realtors' ), 'panel' => 'sp_realtors_theme' )
	);
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: item number */
		$label_prefix = sprintf( __( 'Item %d', 'sp-realtors' ), $i );
		$add_field( "trust_{$i}_icon", 'spr_theme_trust', $label_prefix . ': ' . __( 'Icon', 'sp-realtors' ), 'select', 'sanitize_key', array( 'choices' => sp_realtors_icon_choices() ) );
		$add_field( "trust_{$i}_title", 'spr_theme_trust', $label_prefix . ': ' . __( 'Title', 'sp-realtors' ), 'text', 'sanitize_text_field' );
		$add_field( "trust_{$i}_text", 'spr_theme_trust', $label_prefix . ': ' . __( 'Text', 'sp-realtors' ), 'textarea', 'sanitize_textarea_field' );
	}

	/* --------------------------------------------------------------
	 * Browse by Requirement (4 items).
	 * ----------------------------------------------------------- */
	$wp_customize->add_section(
		'spr_theme_requirement',
		array( 'title' => __( 'Browse by Requirement', 'sp-realtors' ), 'panel' => 'sp_realtors_theme' )
	);
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: item number */
		$label_prefix = sprintf( __( 'Card %d', 'sp-realtors' ), $i );
		$add_field( "requirement_{$i}_title", 'spr_theme_requirement', $label_prefix . ': ' . __( 'Title', 'sp-realtors' ), 'text', 'sanitize_text_field' );
		$add_field( "requirement_{$i}_text", 'spr_theme_requirement', $label_prefix . ': ' . __( 'Text', 'sp-realtors' ), 'textarea', 'sanitize_textarea_field' );
		$add_field( "requirement_{$i}_image", 'spr_theme_requirement', $label_prefix . ': ' . __( 'Image', 'sp-realtors' ), 'image', 'absint' );
		$add_field( "requirement_{$i}_url", 'spr_theme_requirement', $label_prefix . ': ' . __( 'Link URL', 'sp-realtors' ), 'url', 'esc_url_raw' );
	}

	/* --------------------------------------------------------------
	 * Why Choose Us (5 items).
	 * ----------------------------------------------------------- */
	$wp_customize->add_section(
		'spr_theme_why',
		array( 'title' => __( 'Why Choose Us', 'sp-realtors' ), 'panel' => 'sp_realtors_theme' )
	);
	for ( $i = 1; $i <= 5; $i++ ) {
		/* translators: %d: item number */
		$label_prefix = sprintf( __( 'Item %d', 'sp-realtors' ), $i );
		$add_field( "why_{$i}_icon", 'spr_theme_why', $label_prefix . ': ' . __( 'Icon', 'sp-realtors' ), 'select', 'sanitize_key', array( 'choices' => sp_realtors_icon_choices() ) );
		$add_field( "why_{$i}_title", 'spr_theme_why', $label_prefix . ': ' . __( 'Title', 'sp-realtors' ), 'text', 'sanitize_text_field' );
		$add_field( "why_{$i}_text", 'spr_theme_why', $label_prefix . ': ' . __( 'Text', 'sp-realtors' ), 'textarea', 'sanitize_textarea_field' );
	}

	/* --------------------------------------------------------------
	 * CTA Banner.
	 * ----------------------------------------------------------- */
	$wp_customize->add_section(
		'spr_theme_cta',
		array( 'title' => __( 'CTA Banner', 'sp-realtors' ), 'panel' => 'sp_realtors_theme' )
	);
	$add_field( 'cta_heading', 'spr_theme_cta', __( 'Heading', 'sp-realtors' ), 'text', 'sanitize_text_field' );
	$add_field( 'cta_text', 'spr_theme_cta', __( 'Text', 'sp-realtors' ), 'textarea', 'sanitize_textarea_field' );
	$add_field( 'cta_button_label', 'spr_theme_cta', __( 'Button label', 'sp-realtors' ), 'text', 'sanitize_text_field' );

	/* --------------------------------------------------------------
	 * About Page (4 stats + 4 approach steps).
	 * ----------------------------------------------------------- */
	$wp_customize->add_section(
		'spr_theme_about',
		array( 'title' => __( 'About Page', 'sp-realtors' ), 'panel' => 'sp_realtors_theme' )
	);
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: item number */
		$label_prefix = sprintf( __( 'Stat %d', 'sp-realtors' ), $i );
		$add_field( "stat_{$i}_number", 'spr_theme_about', $label_prefix . ': ' . __( 'Number', 'sp-realtors' ), 'text', 'sanitize_text_field' );
		$add_field( "stat_{$i}_label", 'spr_theme_about', $label_prefix . ': ' . __( 'Label', 'sp-realtors' ), 'text', 'sanitize_text_field' );
	}
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: item number */
		$label_prefix = sprintf( __( 'Step %d', 'sp-realtors' ), $i );
		$add_field( "approach_{$i}_title", 'spr_theme_about', $label_prefix . ': ' . __( 'Title', 'sp-realtors' ), 'text', 'sanitize_text_field' );
		$add_field( "approach_{$i}_text", 'spr_theme_about', $label_prefix . ': ' . __( 'Text', 'sp-realtors' ), 'textarea', 'sanitize_textarea_field' );
	}

	/* --------------------------------------------------------------
	 * Footer.
	 * ----------------------------------------------------------- */
	$wp_customize->add_section(
		'spr_theme_footer',
		array( 'title' => __( 'Footer', 'sp-realtors' ), 'panel' => 'sp_realtors_theme' )
	);
	$add_field( 'footer_tagline', 'spr_theme_footer', __( 'Tagline', 'sp-realtors' ), 'text', 'sanitize_text_field' );
	$add_field( 'footer_copyright', 'spr_theme_footer', __( 'Copyright text', 'sp-realtors' ), 'text', 'sanitize_text_field' );

	if ( $wp_customize->selective_refresh ) {
		$wp_customize->selective_refresh->add_partial(
			'spr_theme_footer_tagline',
			array(
				'selector'        => '.sp-realtors-footer__tagline',
				'settings'        => array( 'spr_theme_footer_tagline' ),
				'render_callback' => function () {
					return esc_html( sp_realtors_theme_mod( 'footer_tagline' ) );
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'spr_theme_footer_copyright',
			array(
				'selector'        => '.sp-realtors-footer__copyright-text',
				'settings'        => array( 'spr_theme_footer_copyright' ),
				'render_callback' => function () {
					return esc_html( sp_realtors_theme_mod( 'footer_copyright' ) );
				},
			)
		);
	}

	/* --------------------------------------------------------------
	 * Colors.
	 * ----------------------------------------------------------- */
	$wp_customize->add_section(
		'spr_theme_colors',
		array( 'title' => __( 'Colors', 'sp-realtors' ), 'panel' => 'sp_realtors_theme' )
	);
	foreach ( array(
		'color_navy'  => __( 'Navy', 'sp-realtors' ),
		'color_blue'  => __( 'Blue', 'sp-realtors' ),
		'color_green' => __( 'Green (conversion buttons only)', 'sp-realtors' ),
		'color_gold'  => __( 'Gold (accents only)', 'sp-realtors' ),
	) as $key => $label ) {
		$wp_customize->add_setting(
			'spr_theme_' . $key,
			array(
				'type'              => 'theme_mod',
				'capability'        => 'edit_theme_options',
				'default'           => $defaults[ $key ],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'spr_theme_' . $key,
				array(
					'label'   => $label,
					'section' => 'spr_theme_colors',
				)
			)
		);
	}

	/* --------------------------------------------------------------
	 * Mobile.
	 * ----------------------------------------------------------- */
	$wp_customize->add_section(
		'spr_theme_mobile',
		array( 'title' => __( 'Mobile', 'sp-realtors' ), 'panel' => 'sp_realtors_theme' )
	);
	$add_field(
		'mobile_sticky_bar',
		'spr_theme_mobile',
		__( 'Show sticky Call / WhatsApp bar on mobile', 'sp-realtors' ),
		'checkbox',
		'rest_sanitize_boolean'
	);

	/* --------------------------------------------------------------
	 * Contact Page.
	 * ----------------------------------------------------------- */
	$wp_customize->add_section(
		'spr_theme_contact',
		array( 'title' => __( 'Contact Page', 'sp-realtors' ), 'panel' => 'sp_realtors_theme' )
	);
	$add_field(
		'contact_form_shortcode',
		'spr_theme_contact',
		__( 'Form shortcode override', 'sp-realtors' ),
		'text',
		'sanitize_text_field',
		array( 'description' => __( 'Leave empty to use the built-in enquiry form. E.g. paste a Contact Form 7 shortcode.', 'sp-realtors' ) )
	);
	$add_field(
		'contact_map_url',
		'spr_theme_contact',
		__( 'Map embed URL', 'sp-realtors' ),
		'url',
		'esc_url_raw'
	);
}
add_action( 'customize_register', 'sp_realtors_customize_register' );
