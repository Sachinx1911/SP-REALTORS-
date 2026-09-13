<?php
/**
 * Native enquiry / contact form handler (no plugin dependency).
 *
 * WHY HERE: Form processing is functionality. It works with any theme and
 * never blocks Contact Form 7 / WPForms — the broker can use those instead by
 * pasting their shortcode (see README).
 *
 * Flow: POST → admin-post.php?action=spr_enquiry → nonce → honeypot + time
 * trap → rate limit → sanitize/validate → save as private Enquiry →
 * wp_mail() → redirect back with ?spr_status=… (Post/Redirect/Get).
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Status codes → messages.
 *
 * @return array
 */
function spr_get_enquiry_messages() {
	return apply_filters(
		'spr_enquiry_messages',
		array(
			'sent'    => __( 'Thank you! Your enquiry has been sent. We will call you shortly.', 'sp-realtors-core' ),
			'invalid' => __( 'Please enter your name and a valid phone number.', 'sp-realtors-core' ),
			'expired' => __( 'The form expired. Please try again.', 'sp-realtors-core' ),
			'rate'    => __( 'Please wait a minute before sending another enquiry.', 'sp-realtors-core' ),
			'error'   => __( 'Sorry, something went wrong. Please call or WhatsApp us directly.', 'sp-realtors-core' ),
		)
	);
}

/**
 * Current form status from the redirect (only for the matching form).
 *
 * @param string $form Form id ('contact' or 'property').
 * @return array { status: string, message: string, type: 'success'|'error' } or empty array.
 */
function spr_get_enquiry_status( $form = '' ) {
	// Read-only display flag set by our own redirect; no state change happens here.
	$status = isset( $_GET['spr_status'] ) ? sanitize_key( wp_unslash( $_GET['spr_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$source = isset( $_GET['spr_form'] ) ? sanitize_key( wp_unslash( $_GET['spr_form'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$messages = spr_get_enquiry_messages();
	if ( '' === $status || ! isset( $messages[ $status ] ) ) {
		return array();
	}
	if ( '' !== $form && '' !== $source && $form !== $source ) {
		return array();
	}

	return array(
		'status'  => $status,
		'message' => $messages[ $status ],
		'type'    => 'sent' === $status ? 'success' : 'error',
	);
}

/**
 * Hidden fields every enquiry form MUST contain (nonce, action, honeypot, time trap).
 *
 * Theme overrides of the form template should call this, so security fields
 * can never be forgotten.
 *
 * @param array $args Form args (property_id, source).
 */
function spr_enquiry_hidden_fields( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'property_id' => 0,
			'source'      => 'contact',
		)
	);

	wp_nonce_field( 'spr_enquiry', 'spr_enquiry_nonce' );
	?>
	<input type="hidden" name="action" value="spr_enquiry">
	<input type="hidden" name="spr_source" value="<?php echo esc_attr( sanitize_key( $args['source'] ) ); ?>">
	<input type="hidden" name="spr_property_id" value="<?php echo esc_attr( absint( $args['property_id'] ) ); ?>">
	<input type="hidden" name="spr_ts" value="<?php echo esc_attr( time() ); ?>">
	<div class="spr-hp" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden">
		<label for="spr-hp-<?php echo esc_attr( sanitize_key( $args['source'] ) ); ?>"><?php esc_html_e( 'Leave this field empty', 'sp-realtors-core' ); ?></label>
		<input type="text" id="spr-hp-<?php echo esc_attr( sanitize_key( $args['source'] ) ); ?>" name="spr_hp" value="" tabindex="-1" autocomplete="off">
	</div>
	<?php
}

/**
 * Render the enquiry form (theme template-parts/enquiry-form.php overrides the plugin template).
 *
 * @param array $args {
 *     @type int    $property_id  Property ID (0 for contact page).
 *     @type string $source       'contact' or 'property'.
 *     @type bool   $show_email   Show email field.
 *     @type bool   $show_message Show message field.
 *     @type string $title        Optional heading.
 *     @type string $button_label Submit label.
 * }
 * @return string HTML.
 */
function spr_render_enquiry_form( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'property_id'  => 0,
			'source'       => 'contact',
			'show_email'   => true,
			'show_message' => true,
			'title'        => '',
			'button_label' => __( 'Send Enquiry', 'sp-realtors-core' ),
		)
	);

	$args['source']      = in_array( $args['source'], array( 'contact', 'property' ), true ) ? $args['source'] : 'contact';
	$args['property_id'] = absint( $args['property_id'] );
	$args['status']      = spr_get_enquiry_status( $args['source'] );
	$args['action_url']  = admin_url( 'admin-post.php' );

	ob_start();
	spr_get_template_part( 'enquiry-form', $args );
	return ob_get_clean();
}

/**
 * Keep a string within a length (multibyte-safe when mbstring exists).
 *
 * @param string $text   Text.
 * @param int    $length Max length.
 * @return string
 */
function spr_truncate( $text, $length ) {
	return function_exists( 'mb_substr' ) ? mb_substr( $text, 0, $length ) : substr( $text, 0, $length );
}

/**
 * Normalize an Indian/international phone number. Returns '' if invalid.
 *
 * @param string $phone Raw phone.
 * @return string
 */
function spr_sanitize_phone( $phone ) {
	$phone  = sanitize_text_field( (string) $phone );
	$phone  = preg_replace( '/[^\d+\-\s()]/', '', $phone );
	$digits = preg_replace( '/\D/', '', $phone );
	$len    = strlen( $digits );
	return ( $len >= 10 && $len <= 15 ) ? trim( $phone ) : '';
}

/**
 * Redirect back to the form with a status and stop.
 *
 * @param string $status Status code.
 * @param string $source Form id.
 */
function spr_enquiry_redirect( $status, $source ) {
	$referer = wp_get_referer();
	$url     = $referer ? $referer : home_url( '/' );
	$url     = remove_query_arg( array( 'spr_status', 'spr_form' ), $url );
	$url     = add_query_arg(
		array(
			'spr_status' => $status,
			'spr_form'   => $source,
		),
		$url
	);
	wp_safe_redirect( $url . '#spr-enquiry-' . $source );
	exit;
}

/**
 * Handle submission.
 */
function spr_handle_enquiry() {
	$source = isset( $_POST['spr_source'] ) ? sanitize_key( wp_unslash( $_POST['spr_source'] ) ) : 'contact';
	$source = in_array( $source, array( 'contact', 'property' ), true ) ? $source : 'contact';

	if ( ! isset( $_POST['spr_enquiry_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['spr_enquiry_nonce'] ) ), 'spr_enquiry' ) ) {
		spr_enquiry_redirect( 'expired', $source );
	}

	// Honeypot filled or submitted faster than a human could type → pretend success, save nothing.
	$honeypot  = isset( $_POST['spr_hp'] ) ? sanitize_text_field( wp_unslash( $_POST['spr_hp'] ) ) : '';
	$timestamp = isset( $_POST['spr_ts'] ) ? absint( $_POST['spr_ts'] ) : 0;
	if ( '' !== $honeypot || ( $timestamp && ( time() - $timestamp ) < 3 ) ) {
		spr_enquiry_redirect( 'sent', $source );
	}

	// Simple per-IP rate limit (REMOTE_ADDR only; proxy headers are spoofable).
	$ip       = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rate_key = 'spr_rl_' . md5( $ip . wp_salt( 'nonce' ) );
	if ( $ip && get_transient( $rate_key ) ) {
		spr_enquiry_redirect( 'rate', $source );
	}

	$name    = isset( $_POST['spr_name'] ) ? spr_truncate( sanitize_text_field( wp_unslash( $_POST['spr_name'] ) ), 100 ) : '';
	$phone   = isset( $_POST['spr_phone'] ) ? spr_sanitize_phone( wp_unslash( $_POST['spr_phone'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized in spr_sanitize_phone().
	$email   = isset( $_POST['spr_email'] ) ? sanitize_email( wp_unslash( $_POST['spr_email'] ) ) : '';
	$message = isset( $_POST['spr_message'] ) ? spr_truncate( sanitize_textarea_field( wp_unslash( $_POST['spr_message'] ) ), 2000 ) : '';

	$property_id = isset( $_POST['spr_property_id'] ) ? absint( $_POST['spr_property_id'] ) : 0;
	if ( $property_id && ( 'property' !== get_post_type( $property_id ) || 'publish' !== get_post_status( $property_id ) ) ) {
		$property_id = 0;
	}

	$referer  = wp_get_referer();
	$page_url = $referer ? wp_validate_redirect( esc_url_raw( $referer ), '' ) : '';

	$data = array(
		'name'        => $name,
		'phone'       => $phone,
		'email'       => is_email( $email ) ? $email : '',
		'message'     => $message,
		'property_id' => $property_id,
		'source'      => $source,
		'page_url'    => $page_url,
	);

	$errors = new WP_Error();
	if ( '' === $name ) {
		$errors->add( 'name', __( 'Name is required.', 'sp-realtors-core' ) );
	}
	if ( '' === $phone ) {
		$errors->add( 'phone', __( 'A valid phone number is required.', 'sp-realtors-core' ) );
	}
	if ( '' !== $email && ! is_email( $email ) ) {
		$errors->add( 'email', __( 'Email address is not valid.', 'sp-realtors-core' ) );
	}

	/**
	 * Add custom validation.
	 *
	 * @param WP_Error $errors Errors.
	 * @param array    $data   Sanitized data.
	 */
	$errors = apply_filters( 'spr_enquiry_validate', $errors, $data );

	if ( is_wp_error( $errors ) && $errors->has_errors() ) {
		spr_enquiry_redirect( 'invalid', $source );
	}

	if ( $ip ) {
		set_transient( $rate_key, 1, MINUTE_IN_SECONDS );
	}

	$enquiry_id = spr_create_enquiry( $data );
	$saved      = ! is_wp_error( $enquiry_id );
	$mailed     = spr_send_enquiry_email( $data );

	/**
	 * Fires after an enquiry is received (CRM / Google Sheets integrations).
	 *
	 * @param array    $data       Sanitized data.
	 * @param int|WP_Error $enquiry_id Saved enquiry ID.
	 * @param bool     $mailed     Whether wp_mail() succeeded.
	 */
	do_action( 'spr_enquiry_received', $data, $enquiry_id, $mailed );

	spr_enquiry_redirect( ( $saved || $mailed ) ? 'sent' : 'error', $source );
}
add_action( 'admin_post_spr_enquiry', 'spr_handle_enquiry' );
add_action( 'admin_post_nopriv_spr_enquiry', 'spr_handle_enquiry' );

/**
 * Email the enquiry to the broker.
 *
 * @param array $data Sanitized data.
 * @return bool
 */
function spr_send_enquiry_email( $data ) {
	$to = spr_get_setting( 'enquiry_email' );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	$site = spr_plain_text( get_bloginfo( 'name' ) );

	if ( $data['property_id'] ) {
		$property_title = spr_plain_text( get_the_title( $data['property_id'] ) );
		/* translators: 1: site name, 2: property title */
		$subject = sprintf( __( '[%1$s] New enquiry: %2$s', 'sp-realtors-core' ), $site, $property_title );
	} else {
		/* translators: %s: site name */
		$subject = sprintf( __( '[%s] New contact enquiry', 'sp-realtors-core' ), $site );
	}

	$lines = array(
		__( 'Name', 'sp-realtors-core' ) . ': ' . $data['name'],
		__( 'Phone', 'sp-realtors-core' ) . ': ' . $data['phone'],
	);
	if ( $data['email'] ) {
		$lines[] = __( 'Email', 'sp-realtors-core' ) . ': ' . $data['email'];
	}
	if ( $data['property_id'] ) {
		$lines[] = __( 'Property', 'sp-realtors-core' ) . ': ' . spr_plain_text( get_the_title( $data['property_id'] ) );
		$lines[] = __( 'Link', 'sp-realtors-core' ) . ': ' . get_permalink( $data['property_id'] );
	}
	if ( $data['message'] ) {
		$lines[] = '';
		$lines[] = __( 'Message', 'sp-realtors-core' ) . ':';
		$lines[] = $data['message'];
	}
	if ( $data['page_url'] ) {
		$lines[] = '';
		$lines[] = __( 'Sent from', 'sp-realtors-core' ) . ': ' . $data['page_url'];
	}
	$lines[] = '';
	$lines[] = __( 'All enquiries are also saved in wp-admin → Properties → Enquiries.', 'sp-realtors-core' );

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( $data['email'] ) {
		// Name is sanitize_text_field()-ed (no newlines) so header injection is not possible.
		$headers[] = 'Reply-To: ' . str_replace( array( '"', '<', '>' ), '', $data['name'] ) . ' <' . $data['email'] . '>';
	}

	$mail = apply_filters(
		'spr_enquiry_email',
		array(
			'to'      => $to,
			'subject' => $subject,
			'message' => implode( "\n", $lines ),
			'headers' => $headers,
		),
		$data
	);

	return (bool) wp_mail( $mail['to'], $mail['subject'], $mail['message'], $mail['headers'] );
}
