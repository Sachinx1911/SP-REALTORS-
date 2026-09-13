<?php
/**
 * SPR Dev — Route all wp_mail() through Mailpit (local development ONLY).
 *
 * ही एक must-use plugin आहे. फक्त Docker dev environment मध्ये load होते.
 * Live server वर ही file नसतेच (docker/ folder ship होत नाही).
 *
 * @package SP_Realtors_Dev
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'phpmailer_init',
	function ( $phpmailer ) {
		$phpmailer->isSMTP();
		$phpmailer->Host       = 'mailpit';
		$phpmailer->Port       = 1025;
		$phpmailer->SMTPAuth   = false;
		$phpmailer->SMTPSecure = '';
		$phpmailer->SMTPAutoTLS = false;
	}
);

// Default from address (dev).
add_filter( 'wp_mail_from', function () {
	return 'dev@sprealtors.test';
} );
add_filter( 'wp_mail_from_name', function () {
	return 'SP Realtors (Dev)';
} );
