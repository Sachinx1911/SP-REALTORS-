<?php
/**
 * Branded placeholder image generator (dev tool).
 *
 * Demo properties साठी साधे branded JPG बनवते. GD extension लागते
 * (WordPress container मध्ये असते). Live site वर broker खरे फोटो upload करेल.
 *
 * चालवणे:
 *   docker compose exec wordpress php /var/www/html/wp-content/plugins/sp-realtors-core/../../../.. \
 *   — प्रत्यक्षात खालचा README command वापरा.
 *
 * Output: sp-realtors-core/assets/demo/  मध्ये .jpg files.
 *
 * @package SP_Realtors_Dev
 */

if ( ! extension_loaded( 'gd' ) ) {
	fwrite( STDERR, "GD extension आवश्यक आहे.\n" );
	exit( 1 );
}

$out_dir = getenv( 'SPR_DEMO_OUT' ) ?: __DIR__ . '/../../sp-realtors-core/assets/demo';
if ( ! is_dir( $out_dir ) ) {
	mkdir( $out_dir, 0755, true );
}

// name => [width, height, hex bg, hex accent, label]
$images = array(
	'prop-kharghar'  => array( 1200, 800, '#092B50', '#12B95A', 'Kharghar' ),
	'prop-panvel'    => array( 1200, 800, '#12579A', '#C89A45', 'Panvel' ),
	'prop-ulwe'      => array( 1200, 800, '#0B3D6B', '#12B95A', 'Ulwe' ),
	'prop-vashi'     => array( 1200, 800, '#123A63', '#C89A45', 'Vashi' ),
	'prop-nerul'     => array( 1200, 800, '#0E4C88', '#12B95A', 'Nerul' ),
	'prop-kamothe'   => array( 1200, 800, '#092B50', '#C89A45', 'Kamothe' ),
	'hero'           => array( 1920, 1000, '#092B50', '#12579A', 'SP REALTORS' ),
	'avatar-1'       => array( 240, 240, '#12579A', '#F5F8FA', 'A' ),
	'avatar-2'       => array( 240, 240, '#12B95A', '#F5F8FA', 'B' ),
	'avatar-3'       => array( 240, 240, '#C89A45', '#F5F8FA', 'C' ),
	'area-kharghar'  => array( 640, 440, '#0B3D6B', '#12B95A', 'Kharghar' ),
	'area-panvel'    => array( 640, 440, '#12579A', '#C89A45', 'Panvel' ),
	'area-ulwe'      => array( 640, 440, '#0E4C88', '#12B95A', 'Ulwe' ),
	'area-vashi'     => array( 640, 440, '#123A63', '#C89A45', 'Vashi' ),
);

/**
 * Hex -> RGB.
 */
function spr_hex( $img, $hex ) {
	$hex = ltrim( $hex, '#' );
	return imagecolorallocate(
		$img,
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) )
	);
}

foreach ( $images as $name => $cfg ) {
	list( $w, $h, $bg, $accent, $label ) = $cfg;
	$img = imagecreatetruecolor( $w, $h );

	// Vertical-ish gradient.
	$c1 = array( hexdec( substr( ltrim( $bg, '#' ), 0, 2 ) ), hexdec( substr( ltrim( $bg, '#' ), 2, 2 ) ), hexdec( substr( ltrim( $bg, '#' ), 4, 2 ) ) );
	for ( $y = 0; $y < $h; $y++ ) {
		$t = $y / max( 1, $h );
		$r = (int) ( $c1[0] * ( 1 - $t * 0.35 ) );
		$g = (int) ( $c1[1] * ( 1 - $t * 0.35 ) );
		$b = (int) ( $c1[2] * ( 1 - $t * 0.35 ) );
		$col = imagecolorallocate( $img, $r, $g, $b );
		imageline( $img, 0, $y, $w, $y, $col );
	}

	// Accent bar.
	$acc = spr_hex( $img, $accent );
	imagefilledrectangle( $img, 0, $h - max( 6, (int) ( $h * 0.02 ) ), $w, $h, $acc );

	// Label text (built-in font, scaled by repeating).
	$white = imagecolorallocate( $img, 245, 248, 250 );
	$fs    = 5;
	$tw    = imagefontwidth( $fs ) * strlen( $label );
	$th    = imagefontheight( $fs );
	$scale = max( 2, (int) ( $w / 220 ) );
	// draw enlarged by blitting onto temp then resizing.
	$tmp = imagecreatetruecolor( max( 1, $tw ), max( 1, $th ) );
	imagesavealpha( $tmp, true );
	$transparent = imagecolorallocatealpha( $tmp, 0, 0, 0, 127 );
	imagefill( $tmp, 0, 0, $transparent );
	imagestring( $tmp, $fs, 0, 0, $label, $white );
	$dw = $tw * $scale;
	$dh = $th * $scale;
	imagecopyresized( $img, $tmp, (int) ( ( $w - $dw ) / 2 ), (int) ( ( $h - $dh ) / 2 ), 0, 0, $dw, $dh, $tw, $th );
	imagedestroy( $tmp );

	$path = rtrim( $out_dir, '/' ) . '/' . $name . '.jpg';
	imagejpeg( $img, $path, 82 );
	imagedestroy( $img );
	echo "wrote {$path}\n";
}

echo "Done.\n";
