<?php
/**
 * Plugin smoke tests (dev only).
 *
 * चालवणे: docker compose exec -T -e HTTP_HOST=localhost:8080 wpcli wp eval-file /tests/plugin-smoke.php --user=1
 *
 * @package SP_Realtors_Dev
 */

$pass = 0;
$fail = 0;

$check = function ( $label, $condition, $detail = '' ) use ( &$pass, &$fail ) {
	if ( $condition ) {
		$pass++;
		WP_CLI::log( "  PASS  {$label}" );
	} else {
		$fail++;
		WP_CLI::log( "  FAIL  {$label}" . ( $detail ? " → {$detail}" : '' ) );
	}
};

WP_CLI::log( '--- Registration ---' );
foreach ( array( 'property', 'testimonial', 'spr_enquiry' ) as $pt ) {
	$check( "post type {$pt}", post_type_exists( $pt ) );
}
$check( 'property archive link', false !== strpos( (string) get_post_type_archive_link( 'property' ), '/properties/' ), get_post_type_archive_link( 'property' ) );
$check( 'meta registered _spr_price', registered_meta_key_exists( 'post', '_spr_price', 'property' ) );

WP_CLI::log( '--- Helpers ---' );
$check( 'price 1.35 Cr', '₹1.35 Cr' === spr_format_price( 13500000 ), spr_format_price( 13500000 ) );
$check( 'price 75 L', '₹75 L' === spr_format_price( 7500000 ), spr_format_price( 7500000 ) );
$check( 'rent 18,000/month', '₹18,000/month' === spr_format_price( 18000, 'rent' ), spr_format_price( 18000, 'rent' ) );
$check( 'indian grouping', '12,34,567' === spr_number_format_indian( 1234567 ), spr_number_format_indian( 1234567 ) );
$check( 'map: iframe accepted', 0 === strpos( spr_sanitize_map_url( '<iframe src="https://www.google.com/maps/embed?pb=abc" width="600"></iframe>' ), 'https://www.google.com/maps/embed' ) );
$check( 'map: evil host rejected', '' === spr_sanitize_map_url( 'https://evil.example.com/maps/embed?pb=1' ) );
$check( 'map: javascript rejected', '' === spr_sanitize_map_url( 'javascript:alert(1)' ) );
$check( 'phone valid', '' !== spr_sanitize_phone( '+91 98765 43210' ) );
$check( 'phone too short rejected', '' === spr_sanitize_phone( '12345' ) );
$check( 'amenities allow-list', array( 'gym' ) === spr_sanitize_amenities( array( 'gym', '<script>', 'bogus' ) ) );

WP_CLI::log( '--- Demo import ---' );
require_once SPR_PATH . 'includes/demo-content.php';
$result = spr_import_demo_content( true );
$check( 'import returns true', true === $result, is_wp_error( $result ) ? $result->get_error_message() : '' );
$count = wp_count_posts( 'property' );
$check( '6 published properties', 6 === (int) $count->publish, (string) $count->publish );
$check( '3 testimonials', 3 === (int) wp_count_posts( 'testimonial' )->publish );
$check( 'front page set', 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) );
$check( 'about-us page', (bool) get_page_by_path( 'about-us' ) );

$re = spr_import_demo_content( true );
$check( 're-import creates no duplicates', 6 === (int) wp_count_posts( 'property' )->publish );

WP_CLI::log( '--- Property data ---' );
$ids = get_posts( array( 'post_type' => 'property', 'title' => '2 BHK Apartment in Sector 20, Kharghar', 'fields' => 'ids' ) );
$p   = spr_get_property( $ids[0] );
$check( 'price_formatted', '₹1.35 Cr' === $p['price_formatted'], $p['price_formatted'] );
$check( 'location Kharghar', 'Kharghar' === $p['location'] );
$check( 'thumbnail set', $p['thumbnail_id'] > 0 );
$check( 'gallery has 3 images', 3 === count( $p['gallery'] ), (string) count( $p['gallery'] ) );
$check( 'amenities labels', isset( $p['amenities']['gym'] ) );
$check( 'highlights lines', 4 === count( $p['highlights'] ) );

update_option( 'spr_business', array_merge( (array) get_option( 'spr_business' ), array( 'whatsapp' => '910000000000', 'phone' => '+91 00000 00000' ) ) );
$wa = spr_property_whatsapp_url( $ids[0] );
$check( 'whatsapp url', 0 === strpos( $wa, 'https://wa.me/910000000000?text=' ), $wa );
$check( 'whatsapp message text', false !== strpos( rawurldecode( $wa ), 'I am interested in 2 BHK Apartment in Sector 20, Kharghar in Kharghar.' ), rawurldecode( $wa ) );

WP_CLI::log( '--- Featured + queries ---' );
$fq = spr_featured_properties_query( 3 );
$check( 'featured query 3 posts', 3 === $fq->post_count, (string) $fq->post_count );
$all_featured = true;
foreach ( $fq->posts as $post ) {
	$all_featured = $all_featured && get_post_meta( $post->ID, '_spr_featured', true );
}
$check( 'featured are flagged', $all_featured );

$rent = new WP_Query( array_merge( array( 'post_type' => 'property', 'posts_per_page' => -1 ), spr_build_property_query_args( spr_get_filter_values( array( 'purpose' => 'rent' ) ) ) ) );
$check( 'filter purpose=rent → 2', 2 === $rent->post_count, (string) $rent->post_count );

$buy_budget = new WP_Query( array_merge( array( 'post_type' => 'property', 'posts_per_page' => -1 ), spr_build_property_query_args( spr_get_filter_values( array( 'purpose' => 'buy', 'budget' => '1cr-2cr' ) ) ) ) );
$check( 'filter buy + 1cr-2cr → 2', 2 === $buy_budget->post_count, (string) $buy_budget->post_count );

$multi = new WP_Query( array_merge( array( 'post_type' => 'property', 'posts_per_page' => -1 ), spr_build_property_query_args( spr_get_filter_values( array( 'location' => array( 'kharghar', 'vashi' ) ) ) ) ) );
$check( 'filter location[]=kharghar,vashi → 2', 2 === $multi->post_count, (string) $multi->post_count );

$sorted = new WP_Query( array_merge( array( 'post_type' => 'property', 'posts_per_page' => -1 ), spr_build_property_query_args( spr_get_filter_values( array( 'sort' => 'price_desc' ) ) ) ) );
$first  = $sorted->posts ? absint( get_post_meta( $sorted->posts[0]->ID, '_spr_price', true ) ) : 0;
$check( 'sort price_desc first = 4.5 Cr', 45000000 === $first, (string) $first );

$related = spr_related_properties_query( $ids[0], 3 );
$check( 'related query excludes self', ! in_array( $ids[0], wp_list_pluck( $related->posts, 'ID' ), true ) );

WP_CLI::log( '--- Meta box save (nonce + caps + sanitize) ---' );
$_POST = array(
	'spr_property_meta_nonce' => wp_create_nonce( 'spr_save_property_meta' ),
	'spr_meta'                => array(
		'_spr_price'     => '9900000abc',
		'_spr_status'    => 'hacked-status',
		'_spr_rera'      => '<b>P5200</b>',
		'_spr_map_url'   => 'https://evil.example.com/x',
		'_spr_amenities' => array( 'pool', 'nope' ),
		'_spr_gallery'   => '999999,abc',
	),
);
spr_save_property_meta( $ids[0] );
$check( 'price sanitized to int', 9900000 === absint( get_post_meta( $ids[0], '_spr_price', true ) ), get_post_meta( $ids[0], '_spr_price', true ) );
$check( 'invalid status → default', 'ready' === get_post_meta( $ids[0], '_spr_status', true ), get_post_meta( $ids[0], '_spr_status', true ) );
$check( 'rera tags stripped', 'P5200' === get_post_meta( $ids[0], '_spr_rera', true ), get_post_meta( $ids[0], '_spr_rera', true ) );
$check( 'evil map removed', '' === get_post_meta( $ids[0], '_spr_map_url', true ) );
$check( 'amenities filtered', array( 'pool' ) === get_post_meta( $ids[0], '_spr_amenities', true ) );
$check( 'featured unchecked → removed', ! get_post_meta( $ids[0], '_spr_featured', true ) );
$check( 'bad gallery ids dropped', ! get_post_meta( $ids[0], '_spr_gallery', true ) );

$_POST['spr_property_meta_nonce'] = 'bad-nonce';
$_POST['spr_meta']['_spr_price']  = '1';
spr_save_property_meta( $ids[0] );
$check( 'bad nonce → nothing saved', 9900000 === absint( get_post_meta( $ids[0], '_spr_price', true ) ) );

wp_set_current_user( 0 );
$_POST['spr_property_meta_nonce'] = wp_create_nonce( 'spr_save_property_meta' );
spr_save_property_meta( $ids[0] );
$check( 'logged-out user → nothing saved', 9900000 === absint( get_post_meta( $ids[0], '_spr_price', true ) ) );
wp_set_current_user( 1 );
$_POST = array();

// Restore demo values for the front-end tests.
update_post_meta( $ids[0], '_spr_price', 13500000 );
update_post_meta( $ids[0], '_spr_featured', true );
update_post_meta( $ids[0], '_spr_map_url', 'https://www.google.com/maps?q=Sector+20+Kharghar,+Navi+Mumbai&output=embed' );
delete_post_meta( $ids[0], '_spr_rera' );
spr_flush_property_cache();

WP_CLI::log( '--- Schema ---' );
$schema = spr_get_listing_schema( $ids[0] );
$check( 'listing schema type', 'RealEstateListing' === $schema['@type'] );
$check( 'offer INR', isset( $schema['offers']['priceCurrency'] ) && 'INR' === $schema['offers']['priceCurrency'] );
$check( 'about = Apartment', 'Apartment' === $schema['about']['@type'] );

WP_CLI::log( '--- Nonces for HTTP tests (logged-out) ---' );
wp_set_current_user( 0 );
WP_CLI::log( 'LOADMORE_NONCE=' . wp_create_nonce( 'spr_load_more' ) );
WP_CLI::log( 'ENQUIRY_NONCE=' . wp_create_nonce( 'spr_enquiry' ) );
WP_CLI::log( 'PROPERTY_ID=' . $ids[0] );

WP_CLI::log( '' );
WP_CLI::log( "RESULT: {$pass} passed, {$fail} failed" );
