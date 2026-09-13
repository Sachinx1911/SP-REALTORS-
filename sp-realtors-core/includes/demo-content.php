<?php
/**
 * Demo content importer / remover.
 *
 * WHY HERE: Sample data is content. Loaded only when an admin clicks
 * "Import demo content" (Settings → SP Realtors). Every item is tagged with
 * _spr_demo = 1; "Remove" moves those items to the Trash (never permanent).
 *
 * All names, prices and details below are fictional samples.
 *
 * @package SP_Realtors_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sideload a bundled demo image into the Media Library (reuses it if already imported).
 *
 * @param string $file  File name inside assets/demo/ (e.g. prop-kharghar.jpg).
 * @param string $alt   Alt text.
 * @return int Attachment ID or 0.
 */
function spr_demo_import_image( $file, $alt = '' ) {
	$file = sanitize_file_name( $file );
	$path = SPR_PATH . 'assets/demo/' . $file;

	if ( ! file_exists( $path ) ) {
		return 0;
	}

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_spr_demo_file', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $file, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( $file );
	if ( ! $tmp || ! copy( $path, $tmp ) ) {
		return 0;
	}

	$attachment_id = media_handle_sideload(
		array(
			'name'     => $file,
			'tmp_name' => $tmp,
		),
		0,
		$alt
	);

	if ( is_wp_error( $attachment_id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}

	update_post_meta( $attachment_id, '_spr_demo', 1 );
	update_post_meta( $attachment_id, '_spr_demo_file', $file );
	if ( $alt ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
	}

	return (int) $attachment_id;
}

/**
 * Demo properties.
 *
 * @return array
 */
function spr_demo_properties() {
	$map = 'https://www.google.com/maps?q=%s,+Navi+Mumbai&output=embed';

	return array(
		array(
			'title'    => '2 BHK Apartment in Sector 20, Kharghar',
			'excerpt'  => 'Spacious 2 BHK close to Kharghar railway station and Central Park.',
			'content'  => "<!-- wp:paragraph -->\n<p>A bright, well-ventilated 2 BHK apartment in a gated society in Sector 20, Kharghar. Walking distance to the railway station, schools and Central Park, with quick access to the Sion–Panvel Highway.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>The flat has a large living room with balcony, modular kitchen and two bedrooms with attached wardrobes. Ideal for families and working professionals.</p>\n<!-- /wp:paragraph -->",
			'image'    => 'prop-kharghar.jpg',
			'gallery'  => array( 'prop-ulwe.jpg', 'prop-nerul.jpg' ),
			'terms'    => array(
				'property_location'      => 'kharghar',
				'property_type'          => 'apartment',
				'property_purpose'       => 'buy',
				'property_configuration' => '2-bhk',
			),
			'meta'     => array(
				'_spr_price'      => 13500000,
				'_spr_area'       => 1050,
				'_spr_area_unit'  => 'sqft',
				'_spr_bedrooms'   => 2,
				'_spr_bathrooms'  => 2,
				'_spr_furnishing' => 'semi',
				'_spr_status'     => 'ready',
				'_spr_featured'   => true,
				'_spr_address'    => 'Sector 20, Kharghar, Navi Mumbai',
				'_spr_map_url'    => sprintf( $map, 'Sector+20+Kharghar' ),
				'_spr_possession' => 'Immediate',
				'_spr_highlights' => "5 min walk to Kharghar station\nNear Central Park & schools\nCovered car parking\nEast-facing balcony",
				'_spr_amenities'  => array( 'lift', 'parking', 'security', 'power-backup', 'gym', 'cctv' ),
			),
		),
		array(
			'title'    => '1 BHK Flat for Rent in New Panvel',
			'excerpt'  => 'Ready-to-move 1 BHK on rent near Panvel station.',
			'content'  => "<!-- wp:paragraph -->\n<p>Neat 1 BHK flat available on rent in New Panvel, 10 minutes from Panvel railway station. Suitable for small families and bachelors (company lease preferred).</p>\n<!-- /wp:paragraph -->",
			'image'    => 'prop-panvel.jpg',
			'gallery'  => array( 'prop-kamothe.jpg' ),
			'terms'    => array(
				'property_location'      => 'panvel',
				'property_type'          => 'apartment',
				'property_purpose'       => 'rent',
				'property_configuration' => '1-bhk',
			),
			'meta'     => array(
				'_spr_price'      => 18000,
				'_spr_area'       => 650,
				'_spr_area_unit'  => 'sqft',
				'_spr_bedrooms'   => 1,
				'_spr_bathrooms'  => 1,
				'_spr_furnishing' => 'semi',
				'_spr_status'     => 'ready',
				'_spr_featured'   => true,
				'_spr_address'    => 'New Panvel, Navi Mumbai',
				'_spr_map_url'    => sprintf( $map, 'New+Panvel' ),
				'_spr_possession' => 'Immediate',
				'_spr_highlights' => "10 min to Panvel station\nMarket and hospital nearby\n24x7 water supply",
				'_spr_amenities'  => array( 'lift', 'parking', 'security' ),
			),
		),
		array(
			'title'    => '3 BHK Premium Apartment in Ulwe',
			'excerpt'  => 'Under-construction 3 BHK near the upcoming Navi Mumbai International Airport.',
			'content'  => "<!-- wp:paragraph -->\n<p>Premium 3 BHK residences in Ulwe, minutes from the Navi Mumbai International Airport and the Atal Setu (MTLR) connector. Modern amenities and excellent long-term appreciation potential.</p>\n<!-- /wp:paragraph -->",
			'image'    => 'prop-ulwe.jpg',
			'gallery'  => array( 'prop-kharghar.jpg', 'prop-vashi.jpg' ),
			'terms'    => array(
				'property_location'      => 'ulwe',
				'property_type'          => 'apartment',
				'property_purpose'       => 'buy',
				'property_configuration' => '3-bhk',
			),
			'meta'     => array(
				'_spr_price'      => 18500000,
				'_spr_area'       => 1450,
				'_spr_area_unit'  => 'sqft',
				'_spr_bedrooms'   => 3,
				'_spr_bathrooms'  => 3,
				'_spr_furnishing' => 'unfurnished',
				'_spr_status'     => 'under-construction',
				'_spr_featured'   => true,
				'_spr_address'    => 'Sector 19, Ulwe, Navi Mumbai',
				'_spr_map_url'    => sprintf( $map, 'Ulwe' ),
				'_spr_possession' => 'December 2027',
				'_spr_highlights' => "Near Navi Mumbai International Airport\nClubhouse with swimming pool\nPlayground and landscaped garden",
				'_spr_amenities'  => array( 'lift', 'parking', 'pool', 'clubhouse', 'gym', 'garden', 'play-area', 'security' ),
			),
		),
		array(
			'title'    => 'Furnished Office Space for Rent in Vashi',
			'excerpt'  => 'Plug-and-play office near Vashi station.',
			'content'  => "<!-- wp:paragraph -->\n<p>Fully furnished office space in a commercial complex in Vashi with workstations, a cabin, a conference room and pantry. Close to Vashi railway station and Palm Beach Road.</p>\n<!-- /wp:paragraph -->",
			'image'    => 'prop-vashi.jpg',
			'gallery'  => array(),
			'terms'    => array(
				'property_location' => 'vashi',
				'property_type'     => 'office',
				'property_purpose'  => 'rent',
			),
			'meta'     => array(
				'_spr_price'      => 85000,
				'_spr_area'       => 900,
				'_spr_area_unit'  => 'sqft',
				'_spr_bathrooms'  => 2,
				'_spr_furnishing' => 'fully',
				'_spr_status'     => 'ready',
				'_spr_address'    => 'Sector 17, Vashi, Navi Mumbai',
				'_spr_map_url'    => sprintf( $map, 'Sector+17+Vashi' ),
				'_spr_highlights' => "20 workstations + 1 cabin\nConference room\n2 reserved parking slots",
				'_spr_amenities'  => array( 'lift', 'parking', 'power-backup', 'cctv', 'security' ),
			),
		),
		array(
			'title'    => '4 BHK Independent Villa in Nerul',
			'excerpt'  => 'Spacious resale villa with private garden in Nerul.',
			'content'  => "<!-- wp:paragraph -->\n<p>A rare independent 4 BHK villa in a quiet lane of Nerul with a private garden, terrace and two covered parkings. Close to DY Patil Stadium and Seawoods Grand Central.</p>\n<!-- /wp:paragraph -->",
			'image'    => 'prop-nerul.jpg',
			'gallery'  => array( 'prop-panvel.jpg' ),
			'terms'    => array(
				'property_location'      => 'nerul',
				'property_type'          => 'villa',
				'property_purpose'       => 'buy',
				'property_configuration' => '4-bhk-plus',
			),
			'meta'     => array(
				'_spr_price'      => 45000000,
				'_spr_area'       => 3200,
				'_spr_area_unit'  => 'sqft',
				'_spr_bedrooms'   => 4,
				'_spr_bathrooms'  => 4,
				'_spr_furnishing' => 'semi',
				'_spr_status'     => 'resale',
				'_spr_address'    => 'Sector 6, Nerul, Navi Mumbai',
				'_spr_map_url'    => sprintf( $map, 'Nerul' ),
				'_spr_possession' => 'Immediate',
				'_spr_highlights' => "Private garden and terrace\n2 covered car parks\nNear Seawoods Grand Central",
				'_spr_amenities'  => array( 'parking', 'garden', 'security', 'power-backup' ),
			),
		),
		array(
			'title'    => 'Residential Plot in Kamothe',
			'excerpt'  => 'Clear-title residential plot close to Kamothe sector road.',
			'content'  => "<!-- wp:paragraph -->\n<p>2,000 sq.ft residential plot with clear title in Kamothe, suitable for a bungalow or small building. Good road access and quick connectivity to Kharghar and Panvel.</p>\n<!-- /wp:paragraph -->",
			'image'    => 'prop-kamothe.jpg',
			'gallery'  => array(),
			'terms'    => array(
				'property_location' => 'kamothe',
				'property_type'     => 'plot',
				'property_purpose'  => 'buy',
			),
			'meta'     => array(
				'_spr_price'      => 7500000,
				'_spr_area'       => 2000,
				'_spr_area_unit'  => 'sqft',
				'_spr_status'     => 'new-launch',
				'_spr_address'    => 'Kamothe, Navi Mumbai',
				'_spr_map_url'    => sprintf( $map, 'Kamothe' ),
				'_spr_highlights' => "Clear title\n30 ft wide access road\nNear Sion–Panvel Highway",
			),
		),
	);
}

/**
 * Demo testimonials.
 *
 * @return array
 */
function spr_demo_testimonials() {
	return array(
		array(
			'title'  => 'Rahul & Priya M.',
			'text'   => 'SP REALTORS helped us find our first home in Kharghar within our budget. Every visit was on time and the paperwork was explained clearly.',
			'detail' => 'Bought a 2 BHK in Kharghar',
			'image'  => 'avatar-1.jpg',
		),
		array(
			'title'  => 'Sneha K.',
			'text'   => 'Very honest and quick. They shortlisted only relevant flats and I moved into my rental in Panvel in less than a week.',
			'detail' => 'Rented a 1 BHK in Panvel',
			'image'  => 'avatar-2.jpg',
		),
		array(
			'title'  => 'Amit D.',
			'text'   => 'Professional guidance on investment options near the new airport. I would recommend them to anyone buying in Navi Mumbai.',
			'detail' => 'Investor, Ulwe',
			'image'  => 'avatar-3.jpg',
		),
	);
}

/**
 * Import everything.
 *
 * @param bool $setup_site Also create pages, menus and set the static homepage.
 * @return true|WP_Error
 */
function spr_import_demo_content( $setup_site = true ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error( 'spr_forbidden', __( 'Not allowed.', 'sp-realtors-core' ) );
	}

	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	spr_seed_default_terms();

	// Properties.
	foreach ( spr_demo_properties() as $item ) {
		if ( spr_demo_post_exists( $item['title'], 'property' ) ) {
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => 'property',
				'post_status'  => 'publish',
				'post_title'   => $item['title'],
				'post_excerpt' => $item['excerpt'],
				'post_content' => $item['content'],
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			continue;
		}

		update_post_meta( $post_id, '_spr_demo', 1 );

		foreach ( $item['terms'] as $taxonomy => $slug ) {
			wp_set_object_terms( $post_id, $slug, $taxonomy );
		}
		foreach ( $item['meta'] as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		$thumb = spr_demo_import_image( $item['image'], $item['title'] );
		if ( $thumb ) {
			set_post_thumbnail( $post_id, $thumb );
		}
		$gallery = array();
		foreach ( $item['gallery'] as $file ) {
			$id = spr_demo_import_image( $file, $item['title'] );
			if ( $id ) {
				$gallery[] = $id;
			}
		}
		if ( $gallery ) {
			update_post_meta( $post_id, '_spr_gallery', $gallery );
		}
	}

	// Testimonials.
	foreach ( spr_demo_testimonials() as $index => $item ) {
		if ( spr_demo_post_exists( $item['title'], 'testimonial' ) ) {
			continue;
		}
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'testimonial',
				'post_status'  => 'publish',
				'post_title'   => $item['title'],
				'post_content' => $item['text'],
				'menu_order'   => $index,
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			continue;
		}
		update_post_meta( $post_id, '_spr_demo', 1 );
		update_post_meta( $post_id, '_spr_rating', 5 );
		update_post_meta( $post_id, '_spr_client_meta', $item['detail'] );
		$photo = spr_demo_import_image( $item['image'], $item['title'] );
		if ( $photo ) {
			set_post_thumbnail( $post_id, $photo );
		}
	}

	// Location card images.
	foreach ( array( 'kharghar', 'panvel', 'ulwe', 'vashi' ) as $slug ) {
		$term = get_term_by( 'slug', $slug, 'property_location' );
		if ( $term && ! get_term_meta( $term->term_id, 'spr_location_image', true ) ) {
			$image = spr_demo_import_image( 'area-' . $slug . '.jpg', $term->name );
			if ( $image ) {
				update_term_meta( $term->term_id, 'spr_location_image', $image );
			}
		}
	}

	if ( $setup_site ) {
		spr_demo_setup_pages_and_menus();
	}

	update_option( 'spr_demo_imported', 1 );
	delete_option( 'spr_show_demo_notice' );
	spr_flush_property_cache();

	return true;
}

/**
 * Whether a demo post with this title already exists (prevents duplicates on re-import).
 *
 * @param string $title     Title.
 * @param string $post_type Post type.
 * @return bool
 */
function spr_demo_post_exists( $title, $post_type ) {
	$found = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'title'          => $title,
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	return ! empty( $found );
}

/**
 * Create Home / About Us / Contact Us pages, menus and set the static front page.
 */
function spr_demo_setup_pages_and_menus() {
	$pages = array(
		'home'       => array(
			'title'   => __( 'Home', 'sp-realtors-core' ),
			'content' => '',
		),
		'about-us'   => array(
			'title'   => __( 'About Us', 'sp-realtors-core' ),
			'content' => "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Your trusted real estate partner in Navi Mumbai</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>SP REALTORS helps families, professionals and investors buy, sell and rent homes and commercial spaces across Navi Mumbai — from Vashi and Nerul to Kharghar, Panvel and Ulwe.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>We believe in honest advice, verified listings and complete support from the first site visit to the final paperwork. Properties · People · Possibilities.</p>\n<!-- /wp:paragraph -->",
		),
		'contact-us' => array(
			'title'   => __( 'Contact Us', 'sp-realtors-core' ),
			'content' => "<!-- wp:paragraph -->\n<p>Looking to buy, sell or rent a property in Navi Mumbai? Call, WhatsApp or send us a message and our team will get back to you shortly.</p>\n<!-- /wp:paragraph -->",
		),
	);

	$ids = array();
	foreach ( $pages as $slug => $page ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$ids[ $slug ] = $existing->ID;
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_name'    => $slug,
				'post_title'   => $page['title'],
				'post_content' => $page['content'],
			),
			true
		);
		if ( ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_spr_demo', 1 );
			$ids[ $slug ] = $id;
		}
	}

	if ( ! empty( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
	}

	$registered = get_registered_nav_menus();
	$locations  = get_theme_mod( 'nav_menu_locations', array() );
	$menus      = array(
		'primary' => __( 'Primary Menu', 'sp-realtors-core' ),
		'footer'  => __( 'Footer Menu', 'sp-realtors-core' ),
	);

	foreach ( $menus as $location => $menu_name ) {
		$menu = wp_get_nav_menu_object( $menu_name );
		if ( $menu ) {
			$menu_id = $menu->term_id;
		} else {
			$menu_id = wp_create_nav_menu( $menu_name );
			if ( is_wp_error( $menu_id ) ) {
				continue;
			}

			$items = array(
				array( __( 'Home', 'sp-realtors-core' ), home_url( '/' ), 0 ),
				array( __( 'Properties', 'sp-realtors-core' ), spr_get_properties_url(), 0 ),
				array( __( 'About Us', 'sp-realtors-core' ), '', isset( $ids['about-us'] ) ? $ids['about-us'] : 0 ),
				array( __( 'Contact', 'sp-realtors-core' ), '', isset( $ids['contact-us'] ) ? $ids['contact-us'] : 0 ),
			);

			foreach ( $items as $position => $item ) {
				$args = array(
					'menu-item-title'    => $item[0],
					'menu-item-status'   => 'publish',
					'menu-item-position' => $position + 1,
				);
				if ( $item[2] ) {
					$args['menu-item-object-id'] = $item[2];
					$args['menu-item-object']    = 'page';
					$args['menu-item-type']      = 'post_type';
				} elseif ( $item[1] ) {
					$args['menu-item-url']  = $item[1];
					$args['menu-item-type'] = 'custom';
				} else {
					continue;
				}
				wp_update_nav_menu_item( $menu_id, 0, $args );
			}
		}

		// Assign to the theme location only if the active theme provides it.
		if ( isset( $registered[ $location ] ) && empty( $locations[ $location ] ) ) {
			$locations[ $location ] = $menu_id;
		}
	}

	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Move all demo posts/pages to the Trash (images and menus are kept).
 */
function spr_remove_demo_content() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$ids = get_posts(
		array(
			'post_type'      => array( 'property', 'testimonial', 'page' ),
			'post_status'    => 'any',
			'posts_per_page' => 200,
			'fields'         => 'ids',
			'meta_key'       => '_spr_demo', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);

	$front = (int) get_option( 'page_on_front' );

	foreach ( $ids as $id ) {
		if ( $id === $front ) {
			update_option( 'show_on_front', 'posts' );
			update_option( 'page_on_front', 0 );
		}
		wp_trash_post( $id );
	}

	delete_option( 'spr_demo_imported' );
	spr_flush_property_cache();
}
