<?php
/**
 * "SP Realtors Core" plugin detection + admin notice.
 *
 * WHY HERE: The theme must never go fatal or show a broken front-end just
 * because the data plugin is inactive. Every front-end call into the plugin
 * API is guarded by sp_realtors_core_active().
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the SP Realtors Core plugin is active (its public API is loaded).
 *
 * @return bool
 */
function sp_realtors_core_active() {
	return function_exists( 'spr_get_property' );
}

/**
 * Admin notice when the theme is active but the plugin is not.
 */
function sp_realtors_plugin_notice() {
	if ( sp_realtors_core_active() ) {
		return;
	}
	if ( ! current_user_can( 'install_plugins' ) && ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	if ( get_user_meta( get_current_user_id(), 'sp_realtors_notice_dismissed', true ) ) {
		return;
	}
	?>
	<div class="notice notice-warning is-dismissible sp-realtors-plugin-notice">
		<p>
			<?php
			esc_html_e( 'SP Realtors theme works best with the "SP Realtors Core" plugin active — it powers properties, locations, testimonials and enquiries. Without it, those sections are hidden but the rest of the site keeps working.', 'sp-realtors' );
			?>
		</p>
	</div>
	<script>
	( function () {
		var notice = document.currentScript.previousElementSibling;
		if ( ! notice ) {
			return;
		}
		notice.addEventListener( 'click', function ( event ) {
			if ( ! event.target.classList.contains( 'notice-dismiss' ) ) {
				return;
			}
			var data = new FormData();
			data.append( 'action', 'sp_realtors_dismiss_notice' );
			data.append( 'nonce', '<?php echo esc_js( wp_create_nonce( 'sp_realtors_dismiss_notice' ) ); ?>' );
			fetch( ajaxurl, { method: 'POST', credentials: 'same-origin', body: data } );
		} );
	} )();
	</script>
	<?php
}
add_action( 'admin_notices', 'sp_realtors_plugin_notice' );

/**
 * Remember the dismissal so the notice does not keep coming back.
 */
function sp_realtors_dismiss_notice() {
	check_ajax_referer( 'sp_realtors_dismiss_notice', 'nonce' );
	update_user_meta( get_current_user_id(), 'sp_realtors_notice_dismissed', 1 );
	wp_send_json_success();
}
add_action( 'wp_ajax_sp_realtors_dismiss_notice', 'sp_realtors_dismiss_notice' );
