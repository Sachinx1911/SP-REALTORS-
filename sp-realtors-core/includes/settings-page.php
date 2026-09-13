<?php
/**
 * Settings → SP Realtors (Settings API) + demo content tools + notices.
 *
 * WHY HERE: Plugin-level options (enquiry email, schema, uninstall behaviour)
 * are functionality settings, independent of the theme.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the settings page.
 */
function spr_add_settings_page() {
	add_options_page(
		__( 'SP Realtors Settings', 'sp-realtors-core' ),
		__( 'SP Realtors', 'sp-realtors-core' ),
		'manage_options',
		'spr-settings',
		'spr_render_settings_page'
	);
}
add_action( 'admin_menu', 'spr_add_settings_page' );

/**
 * Register settings, sections and fields.
 */
function spr_register_settings() {
	register_setting(
		'spr_settings_group',
		'spr_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'spr_sanitize_settings',
			'default'           => array(),
			'show_in_rest'      => false,
		)
	);

	add_settings_section( 'spr_section_enquiries', __( 'Enquiries', 'sp-realtors-core' ), '__return_false', 'spr-settings' );
	add_settings_section( 'spr_section_seo', __( 'SEO', 'sp-realtors-core' ), '__return_false', 'spr-settings' );
	add_settings_section( 'spr_section_data', __( 'Data', 'sp-realtors-core' ), '__return_false', 'spr-settings' );

	add_settings_field(
		'spr_enquiry_email',
		__( 'Send enquiries to', 'sp-realtors-core' ),
		'spr_field_enquiry_email',
		'spr-settings',
		'spr_section_enquiries',
		array( 'label_for' => 'spr_enquiry_email' )
	);
	add_settings_field(
		'spr_enable_schema',
		__( 'Structured data', 'sp-realtors-core' ),
		'spr_field_checkbox',
		'spr-settings',
		'spr_section_seo',
		array(
			'key'         => 'enable_schema',
			'label'       => __( 'Output Google structured data (JSON-LD) for listings and the business', 'sp-realtors-core' ),
			'description' => __( 'Business data is skipped automatically if Yoast Local SEO or Rank Math Local SEO is active.', 'sp-realtors-core' ),
		)
	);
	add_settings_field(
		'spr_delete_data',
		__( 'On uninstall', 'sp-realtors-core' ),
		'spr_field_checkbox',
		'spr-settings',
		'spr_section_data',
		array(
			'key'         => 'delete_data_on_uninstall',
			'label'       => __( 'Delete ALL properties, testimonials, enquiries, locations and settings when the plugin is deleted', 'sp-realtors-core' ),
			'description' => __( 'Leave unchecked (recommended). Deactivating the plugin never deletes anything.', 'sp-realtors-core' ),
		)
	);
}
add_action( 'admin_init', 'spr_register_settings' );

/**
 * Sanitize settings.
 *
 * @param mixed $input Raw.
 * @return array
 */
function spr_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$email = isset( $input['enquiry_email'] ) ? sanitize_email( $input['enquiry_email'] ) : '';

	if ( '' !== $email && ! is_email( $email ) ) {
		add_settings_error( 'spr_settings', 'spr_bad_email', __( 'The enquiry email address is not valid. The site admin email will be used.', 'sp-realtors-core' ) );
		$email = '';
	}

	return array(
		'enquiry_email'            => $email ? $email : get_option( 'admin_email' ),
		'enable_schema'            => empty( $input['enable_schema'] ) ? 0 : 1,
		'delete_data_on_uninstall' => empty( $input['delete_data_on_uninstall'] ) ? 0 : 1,
	);
}

/**
 * Email field.
 */
function spr_field_enquiry_email() {
	?>
	<input type="email" id="spr_enquiry_email" name="spr_settings[enquiry_email]" value="<?php echo esc_attr( spr_get_setting( 'enquiry_email' ) ); ?>" class="regular-text">
	<p class="description"><?php esc_html_e( 'Every enquiry is emailed here and also saved under Properties → Enquiries.', 'sp-realtors-core' ); ?></p>
	<?php
}

/**
 * Checkbox field.
 *
 * @param array $args Field args.
 */
function spr_field_checkbox( $args ) {
	$key = $args['key'];
	?>
	<label>
		<input type="checkbox" name="spr_settings[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( (bool) spr_get_setting( $key ) ); ?>>
		<?php echo esc_html( $args['label'] ); ?>
	</label>
	<?php if ( ! empty( $args['description'] ) ) : ?>
		<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
	<?php endif; ?>
	<?php
}

/**
 * Render settings page.
 */
function spr_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$imported = (bool) get_option( 'spr_demo_imported' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'SP Realtors Settings', 'sp-realtors-core' ); ?></h1>

		<p>
			<?php esc_html_e( 'Phone, WhatsApp, email, address and social links are edited in the Customizer:', 'sp-realtors-core' ); ?>
			<a class="button" href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=spr_business' ) ); ?>"><?php esc_html_e( 'Edit Business Info', 'sp-realtors-core' ); ?></a>
		</p>

		<form action="options.php" method="post">
			<?php
			settings_fields( 'spr_settings_group' );
			do_settings_sections( 'spr-settings' );
			submit_button();
			?>
		</form>

		<hr>

		<h2><?php esc_html_e( 'Demo content', 'sp-realtors-core' ); ?></h2>
		<p><?php esc_html_e( 'Adds 6 sample properties, 3 testimonials, Home / About Us / Contact Us pages and menus so you can see the website working. Replace or remove them any time.', 'sp-realtors-core' ); ?></p>

		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" style="display:inline-block;margin-right:12px">
			<input type="hidden" name="action" value="spr_import_demo">
			<?php wp_nonce_field( 'spr_demo', 'spr_demo_nonce' ); ?>
			<p>
				<label><input type="checkbox" name="spr_set_front_page" value="1" checked> <?php esc_html_e( 'Also set "Home" as the homepage and create menus', 'sp-realtors-core' ); ?></label>
			</p>
			<?php submit_button( $imported ? __( 'Import demo content again', 'sp-realtors-core' ) : __( 'Import demo content', 'sp-realtors-core' ), 'primary', 'submit', false ); ?>
		</form>

		<?php if ( $imported ) : ?>
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" style="display:inline-block" onsubmit="return window.confirm(<?php echo esc_attr( wp_json_encode( __( 'Move all demo properties, testimonials and pages to the Trash?', 'sp-realtors-core' ) ) ); ?>);">
				<input type="hidden" name="action" value="spr_remove_demo">
				<?php wp_nonce_field( 'spr_demo', 'spr_demo_nonce' ); ?>
				<p>&nbsp;</p>
				<?php submit_button( __( 'Move demo content to Trash', 'sp-realtors-core' ), 'delete', 'submit', false ); ?>
			</form>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Import handler.
 */
function spr_handle_import_demo() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'sp-realtors-core' ), 403 );
	}
	check_admin_referer( 'spr_demo', 'spr_demo_nonce' );

	require_once SPR_PATH . 'includes/demo-content.php';
	$result = spr_import_demo_content( ! empty( $_POST['spr_set_front_page'] ) );

	wp_safe_redirect( add_query_arg( 'spr_demo', is_wp_error( $result ) ? 'failed' : 'imported', admin_url( 'options-general.php?page=spr-settings' ) ) );
	exit;
}
add_action( 'admin_post_spr_import_demo', 'spr_handle_import_demo' );

/**
 * Remove handler (moves to Trash only).
 */
function spr_handle_remove_demo() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'sp-realtors-core' ), 403 );
	}
	check_admin_referer( 'spr_demo', 'spr_demo_nonce' );

	require_once SPR_PATH . 'includes/demo-content.php';
	spr_remove_demo_content();

	wp_safe_redirect( add_query_arg( 'spr_demo', 'removed', admin_url( 'options-general.php?page=spr-settings' ) ) );
	exit;
}
add_action( 'admin_post_spr_remove_demo', 'spr_handle_remove_demo' );

/**
 * Admin notices: demo offer after activation, demo results, missing contact details.
 */
function spr_admin_notices() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Result notices (read-only flag from our own redirect).
	$demo = isset( $_GET['spr_demo'] ) ? sanitize_key( wp_unslash( $_GET['spr_demo'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$map  = array(
		'imported' => array( 'success', __( 'Demo content imported. Visit your homepage to see it.', 'sp-realtors-core' ) ),
		'removed'  => array( 'success', __( 'Demo content moved to the Trash. Demo images remain in the Media Library.', 'sp-realtors-core' ) ),
		'failed'   => array( 'error', __( 'Demo import failed. Please check file permissions and try again.', 'sp-realtors-core' ) ),
	);
	if ( isset( $map[ $demo ] ) ) {
		printf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>', esc_attr( $map[ $demo ][0] ), esc_html( $map[ $demo ][1] ) );
	}

	$screen = get_current_screen();
	if ( $screen && 'settings_page_spr-settings' === $screen->id ) {
		return;
	}

	if ( get_option( 'spr_show_demo_notice' ) && ! get_option( 'spr_demo_imported' ) ) {
		$dismiss = wp_nonce_url( admin_url( 'admin-post.php?action=spr_dismiss_demo_notice' ), 'spr_dismiss_demo_notice' );
		?>
		<div class="notice notice-info">
			<p>
				<strong><?php esc_html_e( 'SP Realtors Core is active.', 'sp-realtors-core' ); ?></strong>
				<?php esc_html_e( 'Would you like to import demo properties and pages to get started?', 'sp-realtors-core' ); ?>
			</p>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'options-general.php?page=spr-settings' ) ); ?>"><?php esc_html_e( 'Go to demo import', 'sp-realtors-core' ); ?></a>
				<a class="button-link" href="<?php echo esc_url( $dismiss ); ?>"><?php esc_html_e( 'No thanks', 'sp-realtors-core' ); ?></a>
			</p>
		</div>
		<?php
	}

	if ( '' === spr_get_business( 'phone' ) && '' === spr_get_business( 'whatsapp' ) && $screen && in_array( $screen->id, array( 'dashboard', 'edit-property', 'property' ), true ) ) {
		?>
		<div class="notice notice-warning">
			<p>
				<?php esc_html_e( 'Add your phone and WhatsApp number so visitors can call or WhatsApp you from every property.', 'sp-realtors-core' ); ?>
				<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=spr_business' ) ); ?>"><?php esc_html_e( 'Add contact details', 'sp-realtors-core' ); ?></a>
			</p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'spr_admin_notices' );

/**
 * Dismiss the demo notice.
 */
function spr_dismiss_demo_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'sp-realtors-core' ), 403 );
	}
	check_admin_referer( 'spr_dismiss_demo_notice' );
	delete_option( 'spr_show_demo_notice' );
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
	exit;
}
add_action( 'admin_post_spr_dismiss_demo_notice', 'spr_dismiss_demo_notice' );

/**
 * Settings link on the Plugins screen.
 *
 * @param array $links Links.
 * @return array
 */
function spr_settings_action_link( $links ) {
	array_unshift( $links, sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'options-general.php?page=spr-settings' ) ), esc_html__( 'Settings', 'sp-realtors-core' ) ) );
	return $links;
}
add_filter( 'plugin_action_links_' . SPR_BASENAME, 'spr_settings_action_link' );
