# SP REALTORS — Master Development Plan

> हा plan पुढच्या सगळ्या कामाचा "source of truth" आहे. Plan approve झाल्यावर हीच file
> `D:\website projects\sp\PLAN.md` म्हणून project मध्ये copy करायची. प्रत्येक phase पूर्ण झाल्यावर
> खालच्या checklist मध्ये `[x]` करायचे — म्हणजे नवीन session मध्ये कुठून सुरू करायचं ते लगेच कळेल.

---

## 0. Context (का आणि काय)

- **Client:** SP REALTORS (sprealtors.in) — Navi Mumbai real-estate broker.
- **Tagline:** Properties · People · Possibilities
- **Goal:** Production-ready WordPress site. Broker ला **code न लिहिता** wp-admin मधून सगळं manage करता आलं पाहिजे.
- **मुख्य नियम (Plugin territory):** Data/functionality → **Plugin**. Design → **Theme**. Theme बदलली तरी properties, locations, enquiries, contact info गायब होणार नाहीत.
- **Project folder:** `D:\website projects\sp` (सध्या रिकामा, git नाही).
- **User चे निर्णय:**
  - Design reference image user folder मध्ये टाकणार → path: `design/` (उदा. `design/reference.png`).
  - Testing **Docker** वर.
  - **Demo content हवे** (sample properties + testimonials).
- **Machine वर उपलब्ध:** Docker 29.7, Docker Compose v5.5, PHP 8.4 CLI (Windows).
- **User preference:** chat मधली माहिती **मराठीत आणि थोडक्यात**.

---

## 1. Final Folder Structure

```
D:\website projects\sp\
├─ PLAN.md                         ← हा plan
├─ README.md                       ← broker/developer docs
├─ design/                         ← reference image(s) (user देणार)
├─ docker-compose.yml              ← dev only (ship होणार नाही)
├─ docker/
│  ├─ mu-plugins/spr-dev-mailpit.php   ← dev only: wp_mail → Mailpit
│  └─ tools/make-placeholders.php      ← GD ने demo images बनवणे
│
├─ sp-realtors-core/               ← PLUGIN
│  ├─ sp-realtors-core.php
│  ├─ uninstall.php
│  ├─ includes/
│  │  ├─ helpers.php               ← theme वापरणारी public API
│  │  ├─ cpt-property.php
│  │  ├─ cpt-testimonial.php
│  │  ├─ cpt-enquiry.php           ← leads admin मध्ये save (mail fail झाले तरी)
│  │  ├─ taxonomies.php
│  │  ├─ term-meta.php             ← location image
│  │  ├─ meta-register.php         ← register_post_meta (REST)
│  │  ├─ meta-boxes.php
│  │  ├─ meta-save.php
│  │  ├─ admin-columns.php
│  │  ├─ admin-gallery.php
│  │  ├─ settings-page.php         ← Settings API
│  │  ├─ customizer-business.php   ← phone/email/address (option type)
│  │  ├─ query-filters.php
│  │  ├─ ajax-load-more.php
│  │  ├─ enquiry-handler.php
│  │  ├─ shortcodes.php
│  │  ├─ structured-data.php
│  │  ├─ activation.php
│  │  └─ demo-content.php
│  ├─ templates/                   ← fallback templates (theme override करू शकते)
│  │  ├─ card-property.php
│  │  ├─ search-form.php
│  │  └─ enquiry-form.php
│  ├─ assets/admin/{admin.css, admin.js}
│  ├─ assets/demo/*.jpg            ← generated placeholders (compressed)
│  └─ languages/sp-realtors-core.pot
│
├─ sp-realtors/                    ← THEME
│  ├─ style.css, functions.php, screenshot.png
│  ├─ inc/{setup.php, enqueue.php, customizer.php, template-tags.php,
│  │       plugin-notice.php, woocommerce.php}
│  ├─ header.php, footer.php, sidebar.php, comments.php, searchform.php
│  ├─ front-page.php, index.php, page.php, search.php, 404.php
│  ├─ page-about-us.php, page-contact-us.php
│  ├─ archive-property.php, single-property.php, taxonomy-property_location.php
│  ├─ woocommerce.php
│  ├─ template-parts/
│  │  ├─ card-property.php, card-testimonial.php, breadcrumbs.php
│  │  ├─ hero.php, search-card.php, filter-sidebar.php, property-gallery.php
│  │  ├─ enquiry-card.php, mobile-action-bar.php, content-none.php
│  │  └─ section-{trust, featured, requirement, areas, why, testimonials, cta,
│  │              stats, approach}.php
│  ├─ assets/css/{main.css, editor-style.css}
│  ├─ assets/js/main.js
│  ├─ assets/fonts/*.woff2         ← self-hosted (Inter + Playfair Display)
│  ├─ assets/images/*.jpg|svg      ← hero/placeholder, social icons SVG
│  └─ languages/sp-realtors.pot
│
└─ sp-realtors-child/
   ├─ style.css (Template: sp-realtors)
   ├─ functions.php (parent + child style enqueue)
   └─ README.md (customize कसे करायचे)
```

---

## 2. Naming Conventions (सगळीकडे हेच)

| गोष्ट | Plugin | Theme |
|---|---|---|
| Functions | `spr_` | `sp_realtors_` |
| Constants | `SPR_VERSION`, `SPR_PATH`, `SPR_URL`, `SPR_FILE` | `SP_REALTORS_VERSION`, `SP_REALTORS_DIR`, `SP_REALTORS_URI` |
| Text domain | `sp-realtors-core` | `sp-realtors` |
| Handles (CSS/JS) | `spr-admin` | `sp-realtors-main`, `sp-realtors-fonts` |
| Meta keys | `_spr_*` (underscore = hidden from Custom Fields box) | — |
| Options | `spr_business`, `spr_settings`, `spr_demo_imported` | theme_mods `spr_theme_*` |
| Nonces | `spr_property_meta`, `spr_enquiry`, `spr_load_more`, `spr_demo` | — |

- **Headers:** `Requires at least: 6.3` (script `defer` strategy साठी), `Requires PHP: 7.4`, `Tested up to:` latest.
- PHP 7.4 compatible syntax only (no `match`, no named args, no union types, no `str_contains` → `false !== strpos`).
- सगळ्या PHP files च्या top ला `defined( 'ABSPATH' ) || exit;`.
- प्रत्येक file च्या top comment मध्ये "ही file plugin/theme मध्ये का आहे" हे लिहायचे.

---

## 3. Data Model (Plugin)

### 3.1 CPT `property`
- `public`, `show_in_rest => true`, `has_archive => true`, `rewrite => ['slug' => 'properties', 'with_front' => false]` → archive `/properties/`, single `/properties/{slug}/`.
- `supports`: title, editor, thumbnail, excerpt, revisions, custom-fields.
- `menu_icon`: `dashicons-building`, full translatable labels.

### 3.2 CPT `testimonial`
- `public => false`, `show_ui => true`, `show_in_rest => true`, `menu_icon: dashicons-format-quote`.
- Title = client name, editor = quote, thumbnail = photo.
- Meta: `_spr_rating` (1–5 int), `_spr_client_meta` (उदा. "Bought 2 BHK in Kharghar").

### 3.3 CPT `spr_enquiry` (admin-only leads)
- `public => false`, `show_ui => true`, `show_in_menu => 'edit.php?post_type=property'`, capability map → फक्त `edit_others_posts` वाले पाहू शकतात, `create_posts => 'do_not_allow'`.
- Meta: `_spr_name, _spr_phone, _spr_email, _spr_message, _spr_property_id, _spr_source` (contact/property).

### 3.4 Taxonomies (सगळे `show_in_rest`, `show_admin_column`)
| Taxonomy | Rewrite slug | Hierarchical | Default terms (activation seed) |
|---|---|---|---|
| `property_location` | `location` | yes | Kharghar, Panvel, Ulwe, Vashi, Nerul, CBD Belapur, Seawoods, Kamothe, Taloja, Airoli, Ghansoli, Sanpada |
| `property_type` | `property-type` | yes | Apartment, Villa / Bungalow, Row House, Plot, Shop, Office |
| `property_purpose` | `purpose` | yes (फक्त checkbox UI साठी) | Buy (`buy`), Rent (`rent`) |
| `property_configuration` | `configuration` | yes (फक्त checkbox UI साठी) | 1 RK, 1 BHK, 2 BHK, 3 BHK, 4+ BHK |

- Taxonomies ला `query_var => false` (आपण स्वतःचे query vars वापरणार — `location[]` array support साठी; rewrite archives तरीही चालतात).
- `property_location` term meta: `spr_location_image` (attachment ID) — term add/edit form वर media picker ("Areas We Serve" cards साठी).

### 3.5 Property Meta (`register_post_meta`, `show_in_rest`, `auth_callback` = `current_user_can('edit_post', $id)`)
| Key | Type | Sanitize | Admin UI |
|---|---|---|---|
| `_spr_price` | integer (₹) | `absint` | number |
| `_spr_price_label` | string | `sanitize_text_field` | उदा. "Price on Request" (price रिकामा असल्यास) |
| `_spr_area` | integer | `absint` | number |
| `_spr_area_unit` | string | allow-list: sqft/sqm/acre | select |
| `_spr_bedrooms` | integer | `absint` | number |
| `_spr_bathrooms` | integer | `absint` | number |
| `_spr_furnishing` | string | allow-list: unfurnished/semi/fully | select |
| `_spr_status` | string | allow-list: ready, under-construction, new-launch, resale, sold, rented | select (card badge) |
| `_spr_gallery` | array of int | `array_map('absint')` + `wp_attachment_is_image` | media uploader, drag-sort |
| `_spr_address` | string | `sanitize_textarea_field` | textarea |
| `_spr_map_url` | string | Google Maps embed URL allow-list (खाली पहा) | text (iframe paste केला तरी src काढायचा) |
| `_spr_rera` | string | `sanitize_text_field` | text |
| `_spr_possession` | string | `sanitize_text_field` | text (उदा. "Dec 2027") |
| `_spr_highlights` | string | `sanitize_textarea_field` | textarea, एका line ला एक |
| `_spr_amenities` | array of string | `sanitize_key` + allow-list | checkboxes (Lift, Parking, Gym, Pool, Security, Power Backup, Garden, Clubhouse, Play Area, CCTV, Gas Pipeline, Intercom) |
| `_spr_featured` | boolean | `rest_sanitize_boolean` | checkbox |

- **Map sanitize:** host `google.com / www.google.com / maps.google.com` आणि path `/maps/embed` ने सुरू असेल तरच save; output वर iframe आपणच बनवायचा (`esc_url`, `loading="lazy"`, `referrerpolicy`, title).
- Meta box UI: एकच box "Property Details" tabs सह — Pricing · Specs · Location & Map · Gallery · Features · Legal. Admin CSS/JS फक्त `property` edit screen वर enqueue. `wp_enqueue_media()`.
- **Save (`meta-save.php`):** `save_post_property` hook → autosave/revision skip → `wp_verify_nonce` → `current_user_can('edit_post')` → प्रत्येक field sanitize → `update_post_meta` / रिकामे असल्यास `delete_post_meta` → `delete_transient('spr_featured_ids')`.

### 3.6 Options
- `spr_business` (array, Customizer "Business Info" section — **plugin register करतो**, option type → theme बदलली तरी राहते):
  `phone, whatsapp (digits only), email, address, hours, facebook, instagram, youtube, linkedin, x`.
- `spr_settings` (Settings → SP Realtors, Settings API):
  `enquiry_email` (default admin_email), `enable_schema` (bool), `delete_data_on_uninstall` (bool, default off).
- `spr_demo_imported` (bool).

---

## 4. Plugin Public API (`includes/helpers.php`) — theme फक्त हेच वापरेल

| Function | काय करते |
|---|---|
| `spr_get_property( $post_id )` | सगळा meta + terms एका sanitized array मध्ये |
| `spr_format_price( $amount, $purpose )` | Indian format: ₹1.25 Cr / ₹45 L / ₹25,000/month; रिकामे → price_label |
| `spr_get_business( $key )` | `spr_business` मधून value |
| `spr_whatsapp_url( $message = '' )` | `https://wa.me/{digits}?text=` (`rawurlencode`) |
| `spr_property_whatsapp_url( $post_id )` | "Hello SP REALTORS, I am interested in {Title} in {Location}." (translatable `sprintf`) |
| `spr_tel_url()` | `tel:` link |
| `spr_get_budget_ranges( $purpose )` | Buy: under-50l, 50l-1cr, 1cr-2cr, above-2cr · Rent: under-20k, 20k-50k, above-50k (filterable `spr_budget_ranges`) |
| `spr_get_amenities_list()` | key => label (filterable) |
| `spr_get_featured_properties( $count = 3 )` | featured IDs (transient `spr_featured_ids`) → fallback latest |
| `spr_get_related_properties( $post_id, $count = 3 )` | same location |
| `spr_get_map_embed( $post_id )` | safe iframe HTML |
| `spr_get_gallery_ids( $post_id )` | featured image + gallery IDs |
| `spr_render_enquiry_form( $args )` | nonce'd form HTML (template load) |
| `spr_get_template_part( $slug, $args )` | आधी theme मध्ये `locate_template()`, नसेल तर plugin `templates/` |
| `spr_get_filter_values()` | current sanitized filter query vars (sidebar checked state साठी) |

Theme मध्ये check: `sp_realtors_core_active()` = `function_exists( 'spr_get_property' )`.

---

## 5. Plugin Features (तपशील)

### 5.1 Query Filters (`query-filters.php`)
- `query_vars` filter: `location, ptype, purpose, config, budget, sort` (WP reserved `type` टाळला).
- Values: string (`?location=kharghar`) किंवा array (`?location[]=a&location[]=b`) किंवा comma list — सगळे `sanitize_key` array मध्ये normalize.
- `pre_get_posts`: `! is_admin() && $q->is_main_query() && ( is_post_type_archive('property') || is_tax( spr taxonomies ) )` →
  - `tax_query` (relation AND, प्रत्येक taxonomy आत IN)
  - budget → `_spr_price` BETWEEN / >= (NUMERIC)
  - sort: `latest` (default), `price_asc`, `price_desc`, `area_desc` → named meta_query clause (EXISTS OR NOT EXISTS) म्हणजे price नसलेल्या properties गायब होणार नाहीत
  - `posts_per_page` = 9 (filter `spr_archive_per_page`)
  - sold/rented शेवटी दाखवणे — optional, v1 मध्ये नाही.
- Site search (`?s=`) मध्ये `property` include (फक्त front-end main search).

### 5.2 AJAX Load More (`ajax-load-more.php`)
- `wp_ajax_spr_load_more` + `wp_ajax_nopriv_spr_load_more`, `check_ajax_referer('spr_load_more')`.
- POST: `page` (absint) + filter values → same filter builder function reuse (`spr_build_property_query_args()`) → cards HTML `spr_get_template_part('card-property')` → `wp_send_json_success(['html','has_more'])`.
- JS नसेल तर normal `paginate_links()` pagination दिसते (progressive enhancement).

### 5.3 Enquiry / Contact Handler (`enquiry-handler.php`)
- `admin_post_spr_enquiry` + `admin_post_nopriv_spr_enquiry`.
- Checks: nonce → honeypot `spr_hp` रिकामे → rate limit (transient `spr_rl_{md5(ip)}` 60s) → fields sanitize (`name` text, `phone` digits/+ 10–15, `email` optional `is_email`, `message` textarea, `property_id` absint + post_type check) → validation.
- Save as `spr_enquiry` post → `wp_mail()` to `enquiry_email` (Reply-To user email) → `wp_safe_redirect( add_query_arg( 'spr_status', 'sent|error|invalid', referer ) )`.
- Templates मध्ये `spr_status` वाचून success/error message (`sanitize_key` + allow-list).
- Hooks: `do_action('spr_enquiry_received', $data, $enquiry_id)` (CRM integrations साठी).

### 5.4 Shortcodes (`shortcodes.php`)
- `[sp_featured_properties count="3"]`
- `[sp_property_search]`
- `[sp_properties location="kharghar" purpose="buy" count="6"]`
- `[sp_enquiry_form property_id=""]`
- `[sp_business_info field="phone"]`
- सगळे `shortcode_atts` + sanitize, output buffer ने template render.

### 5.5 Structured Data (`structured-data.php`)
- `wp_head` (priority 20) — single property वर `RealEstateListing` (name, url, image, description, datePosted, offers price/INR, address), front page वर `RealEstateAgent` (LocalBusiness subtype) business info मधून.
- `wp_json_encode` + `JSON_UNESCAPED_SLASHES`.
- Filters: `spr_enable_schema`, `spr_schema_listing`, `spr_schema_business`.
- LocalBusiness suppress: Yoast Local SEO (`defined('WPSEO_LOCAL_VERSION')`) किंवा Rank Math Local module active असल्यास. Settings मधून पूर्ण off करता येते.

### 5.6 Admin
- **Columns:** Image, Price (sortable), Location, Type, Purpose, Status, ★ Featured. `restrict_manage_posts` → Location/Purpose dropdown filters.
- **Testimonial columns:** Photo, Rating.
- **Enquiry columns:** Name, Phone, Property link, Date.
- **Settings page:** Settings → SP Realtors (enquiry email, schema toggle, uninstall opt-in, Demo import/remove buttons — `manage_options` + nonce).
- Dashboard "At a glance" मध्ये properties/enquiries count (optional nice-to-have).

### 5.7 Activation / Deactivation / Uninstall
- Activation: CPT + taxonomies register → default terms seed (`term_exists` check) → default options add → `flush_rewrite_rules()` → admin notice "Import demo content?" साठी flag.
- Deactivation: फक्त `flush_rewrite_rules()`.
- `uninstall.php`: `WP_UNINSTALL_PLUGIN` check → `delete_data_on_uninstall` true असेल तरच posts/terms/options delete. Default → काहीच delete नाही.
- Multisite-safe नाही असे README मध्ये नोंद (v1 single-site).

### 5.8 Demo Content (`demo-content.php`)
- Settings page / activation notice वरून **one-click import** (auto नाही — surprise data नको).
- 6 properties (Kharghar 2 BHK Buy, Panvel 1 BHK Rent, Ulwe 3 BHK Buy, Vashi Office Rent, Nerul Villa Buy, Kamothe Plot Buy) — 3 featured, वेगवेगळे status/price.
- 3 testimonials, pages: Home, About Us (`about-us`, intro blocks सह), Contact Us (`contact-us`), menus `primary` + `footer` (theme location assign फक्त theme active असेल तर).
- Images: `assets/demo/*.jpg` (GD ने बनवलेले branded placeholders) → `media_handle_sideload` ने Media Library मध्ये.
- प्रत्येक demo item वर `_spr_demo = 1` → "Remove demo content" button फक्त तेच **Trash** मध्ये पाठवतो (permanent delete नाही).
- Static front page set करायचा का हे import screen वर checkbox (default on).

---

## 6. Theme (Design + Presentation)

### 6.1 `inc/setup.php`
- `load_theme_textdomain`, theme supports: `title-tag, post-thumbnails, automatic-feed-links, custom-logo, html5 (search-form, comment-form, comment-list, gallery, caption, style, script, navigation-widgets), customize-selective-refresh-widgets, responsive-embeds, align-wide, wp-block-styles, editor-styles`, `add_editor_style('assets/css/editor-style.css')`.
- `editor-color-palette`: Navy, Blue, Green, Gold, Off-white, Charcoal, Muted. `editor-font-sizes`: small/normal/large/x-large.
- `register_nav_menus`: `primary`, `footer`.
- `register_sidebar`: `footer-1`, `footer-2` (block widgets compatible), `blog-sidebar`.
- Image sizes: `spr-card` 640×440 crop, `spr-gallery` 1200×800 crop, `spr-thumb` 240×160 crop, `spr-hero` 1920×900 crop, `spr-avatar` 120×120 crop.
- `$content_width = 1200`.
- `inc/woocommerce.php` फक्त `class_exists('WooCommerce')` असल्यास require.

### 6.2 `inc/enqueue.php`
- Fonts: self-hosted `@font-face` in main.css (Inter 400/500/600 variable, Playfair Display 600/700), `font-display: swap`, hero heading font साठी `<link rel="preload">` via `wp_resource_hints`/`wp_head` hook.
- `sp-realtors-main` CSS (version = `filemtime` in dev / `SP_REALTORS_VERSION` in prod).
- `sp-realtors-main` JS footer, `strategy => 'defer'`, no jQuery. `wp_localize_script` → `sprData { ajaxUrl, nonce, archiveUrl, i18n {loading, noMore, menu, close} }`.
- Brand colors → `wp_add_inline_style` `:root{--spr-navy:…}` (Customizer values, `sanitize_hex_color`).
- `add_filter('should_load_separate_core_block_assets','__return_true')` (core ला फक्त वापरलेल्या blocks चीच CSS load करू द्यायची — dequeue पेक्षा safe).
- Comment reply script फक्त singular + comments open.

### 6.3 `inc/customizer.php` (theme_mods — design content)
Panel **"SP Realtors Theme"**, प्रत्येक setting ला `sanitize_callback`, text ला `postMessage` + selective refresh partial:
| Section | Settings |
|---|---|
| Hero | eyebrow, heading, subtext, image (media, absint) |
| Trust Strip | 4 × (icon select, title, text) |
| Browse by Requirement | 4 × (title, text, image, URL `esc_url_raw`) |
| Why Choose Us | 5 × (icon, title, text) |
| CTA Banner | heading, text, button label |
| About Page | 4 stats (number, label), 4 approach steps (title, text) |
| Footer | tagline, copyright text |
| Colors | navy, blue, green, gold (`sanitize_hex_color`) |
| Mobile | sticky Call/WhatsApp bar on/off (`wp_validate_boolean`) |
| Contact Page | form shortcode override (`sanitize_text_field` → `do_shortcode`), map embed URL |

Defaults मध्ये reference design चा copy text (translatable).

### 6.4 `inc/template-tags.php`
`sp_realtors_breadcrumbs()` (Yoast `yoast_breadcrumb` → Rank Math `rank_math_the_breadcrumbs` → स्वतःचे, `BreadcrumbList` markup), `sp_realtors_posted_on()`, `sp_realtors_icon( $name )` (inline SVG sprite, `wp_kses` allow-list), `sp_realtors_theme_mod( $key )` (defaults सह), `sp_realtors_menu_fallback()`, `sp_realtors_social_links()` (रिकामे hide), `sp_realtors_status_label()`.

### 6.5 `inc/plugin-notice.php`
- Plugin inactive → `admin_notices` (फक्त `install_plugins`/`activate_plugins` capability ला) + dismiss (user meta, nonce).
- Front-end: property sections `if ( sp_realtors_core_active() )` मध्ये; नसेल तर sections hide, pages normal चालतात. **कधीही fatal नाही.**

### 6.6 Templates (hierarchy)
| File | काय |
|---|---|
| `header.php` | `language_attributes`, charset, viewport, `wp_head()`, `body_class()`, `wp_body_open()`, skip-link, sticky header 76px: `the_custom_logo()` / site title, `wp_nav_menu(primary)`, phone + WhatsApp button, hamburger (`aria-expanded`, `aria-controls`) |
| `footer.php` | brand + tagline, footer menu, contact block, social, footer widgets, `© date_i18n('Y')`, mobile action bar, `wp_footer()` |
| `front-page.php` | hero + search card → trust → featured → requirement → areas → why → testimonials → CTA; static page असेल तर त्याचा `the_content()` पण (optional section) |
| `archive-property.php` | breadcrumbs, H1, filter sidebar (desktop) / drawer (mobile), result count, sort select, grid, pagination + Load More |
| `taxonomy-property_location.php` | archive-property reuse (`get_template_part`) + location intro (term description) |
| `single-property.php` | gallery + sticky enquiry card, title/price/location header, `the_content()`, specs grid, highlights, amenities, map, RERA (असल्यास), related, `post_class()` |
| `page-about-us.php` | hero, `the_content()` (blocks), stats, approach 4-step, why, CTA |
| `page-contact-us.php` | details cards, form (Customizer shortcode असेल तर तो, नाहीतर native `spr_render_enquiry_form`), map; page `the_content()` पण render → CF7 shortcode page मध्ये टाकला तरी चालेल |
| `page.php`, `index.php`, `search.php`, `404.php`, `comments.php`, `sidebar.php`, `searchform.php`, `woocommerce.php` | standard, `the_content()`, `comments_template()`, `comment_form()`, `paginate_links()` |

### 6.7 `template-parts/card-property.php`
image (`spr-card`, lazy, srcset) + status badge · title (link) · location icon · price · area · beds · baths/furnishing · "View Details" + WhatsApp (green) button. Args ने heading level बदलता येईल (h2/h3).

### 6.8 CSS (`assets/css/main.css`)
- Tokens: `--spr-navy #092B50, --spr-blue #12579A, --spr-green #12B95A, --spr-gold #C89A45, --spr-bg #F5F8FA, --spr-text #1F2933, --spr-muted #5B6B7F, --spr-border #E3E8EE`, radius, shadows, spacing scale, `clamp()` type scale.
- Green फक्त conversion buttons, gold फक्त छोटे accents.
- Layout: mobile-first; breakpoints `≥430, ≥768, ≥1024, ≥1280, ≥1440`; 360/390 base वर test.
- Components: `.site-header, .nav, .btn(--primary/--whatsapp/--outline), .search-card, .card-property, .grid, .filter-drawer, .gallery, .enquiry-card(sticky), .section, .cta-banner, .testimonial, .stats, .steps, .mobile-bar`.
- Focus-visible outlines, `prefers-reduced-motion`, touch targets ≥44px, `overflow-x: clip` safety, WP core classes (`.alignwide, .alignfull, .wp-caption, .screen-reader-text, .sticky, .bypostauthor`).

### 6.9 JS (`assets/js/main.js`, vanilla, ~ <8KB)
1. Mobile nav toggle (aria-expanded, Esc, focus return).
2. Search card Buy/Rent tabs (radio buttons, budget options swap `data-` attributes मधून).
3. Filter drawer (open/close, focus trap, Esc, body scroll lock), desktop auto-submit on change.
4. Gallery (thumbs = buttons, ←/→ keys, `aria-current`, main image swap srcset सह).
5. Load More (fetch + FormData + nonce, `aria-live` result count).
6. Sort select → URL update.
प्रत्येक module element नसेल तर silently skip.

### 6.10 WooCommerce (`inc/woocommerce.php` + `woocommerce.php`)
- Guarded. `woocommerce`, `wc-product-gallery-zoom/lightbox/slider` supports, wrapper `woocommerce_content()` theme container मध्ये, basic CSS spacing. WC नसताना काहीच load नाही.

### 6.11 Child Theme
`style.css` (`Template: sp-realtors`), `functions.php` (parent style dependency ने child style enqueue), README: template override (`template-parts/card-property.php` copy), CSS variables override, हे का वापरायचे.

---

## 7. Design Reference Workflow
1. User `design/` मध्ये image टाकणार → Read tool ने image बघून प्रत्येक screen (Home, About, Properties, Single, Contact, Mobile) चे notes: spacing, font sizes, card layout, icons, copy text.
2. Notes नुसार CSS tokens + Customizer defaults finalize.
3. Image मिळण्यापूर्वी Phase 1–2 (Docker + Plugin) सुरू करता येतात — त्याला design लागत नाही.

---

## 8. Docker Dev Environment
- `docker-compose.yml` services:
  - `db`: `mysql:8.0` (volume `db_data`)
  - `wordpress`: `wordpress:php8.2-apache` → `http://localhost:8080`; mounts: `./sp-realtors-core`, `./sp-realtors`, `./sp-realtors-child`, `./docker/mu-plugins`; `WORDPRESS_DEBUG=1`
  - `wpcli`: `wordpress:cli` (same volumes) — install, plugin/theme activate, `wp i18n make-pot`, `wp rewrite flush`
  - `mailpit`: `axllent/mailpit` → UI `http://localhost:8025`
  - `php74`: `php:7.4-cli` → PHP 7.4 syntax check (`php -l`)
  - (optional) `phpmyadmin` → `http://localhost:8081`
- WP install via wp-cli: admin user/password local-only (`.env` मध्ये, user स्वतः ठेवेल; मी credentials type करणार नाही — user ला command देईन).
- Older WP test: `wp core update --version=<latest-2> --force`.
- PHPCS (WPCS) — optional: `composer` container मध्ये `wp-coding-standards/wpcs` install (package download असल्याने user ची परवानगी घेऊनच).

---

## 9. Build Phases + Progress Checklist

> GitHub: https://github.com/Sachinx1911/SP-REALTORS-.git (branch `main`)

### Dev notes (पुढच्या session साठी महत्त्वाचे)
- WP-CLI: `docker compose exec -T -e HTTP_HOST=localhost:8080 wpcli wp <command>` (admin tasks साठी `--user=1`)
- Plugin tests: `... wpcli wp eval-file /tests/plugin-smoke.php --user=1` (48 checks; demo import पण करतो)
- PHP 7.4 lint: `docker run --rm -v "${PWD}:/app" -w /app php:7.4-cli sh docker/tools/lint.sh`
- Pretty permalinks साठी `.htaccess`: `docker compose cp docker/config/htaccess wordpress:/var/www/html/.htaccess` (नवीन volume बनवल्यावर पुन्हा)
- Admin user `SPREALTORS` (user ने बनवला; password मी वापरत नाही)
- Dev मध्ये `spr_business` मध्ये **fake** WhatsApp `910000000000` / phone `+91 00000 00000` टाकले आहेत (buttons test करण्यासाठी) — launch आधी broker चे खरे number Customizer मधून टाकायचे
- Mailpit: http://localhost:8025 (सगळे mail इथे येतात, खऱ्या inbox मध्ये जात नाहीत)
- `admin_email` / `spr_business.email` / `spr_settings.enquiry_email` dev मध्ये `dev@sprealtors.local` — कधीही खरा personal email इथे टाकू नये (public footer + schema मध्ये दिसतो)
- Theme active आहे, demo content import केलेला आहे (6 properties, 3 testimonials, front page + about-us set)

### Phase 4 dev notes
- `index.php` सध्या property post type साठी पण generic blog loop वापरतो (उगाच ओबडधोबड दिसतं) — Phase 5 चा `archive-property.php` + `template-parts/card-property.php` हे बदलेल.
- `assets/fonts/` रिकामे आहे — सध्या system font stack fallback वापरतोय (योजना §12 प्रमाणे). खरे Inter/Playfair Display woff2 हवे असल्यास पुढच्या session मध्ये download साठी परवानगी विचारायची.
- `screenshot.png` आणि `.pot` files अजून नाहीत (Phase 6).

### Phase 1 — Environment `[x]`
- [x] `PLAN.md` project मध्ये copy, `design/` folder
- [x] `docker-compose.yml`, `.env.example`, mailpit mu-plugin
- [x] Containers चालू (WordPress core 7.1, MySQL 8, Mailpit, phpMyAdmin)
- [x] WP install (user ने केले), permalinks `/%postname%/` + `.htaccess`
- [x] Placeholder images generated (14 jpg, `sp-realtors-core/assets/demo/`)
- [x] `docker/tools/lint.sh` — PHP 7.4 + 8.2 syntax check

### Phase 2 — Plugin core `[x]` (WordPress 7.1 वर tested)
- [x] main file + constants + loader + textdomain
- [x] CPTs (property, testimonial, enquiry) + taxonomies + term meta
- [x] meta register + meta boxes (tabs) + save + gallery uploader
- [x] admin columns + filters (+ ★ featured AJAX toggle, enquiry count badge)
- [x] activation/deactivation/uninstall
- [x] helpers.php API
- [x] Docker वर activate + smoke tests (48/48 pass). Admin screens browser मध्ये user ने पाहणे बाकी.

### Phase 3 — Plugin front features `[x]`
- [x] query filters + AJAX load more
- [x] enquiry handler + enquiry CPT + mail
- [x] shortcodes + fallback templates (+ fallback CSS, wpml-config.xml)
- [x] structured data
- [x] settings page + business customizer section
- [x] demo import/remove
- [x] End-to-end HTTP tests: archive filters (9 cases), tax archive, search, JSON-LD, AJAX Load More (+ bad nonce 403), enquiry (sent / rate / expired / bot / invalid / contact), Mailpit mail, debug.log clean

### Phase 4 — Theme base `[x]`
- [x] style.css, functions.php, setup, enqueue, plugin-notice
- [x] header/footer, nav, skip-link, mobile bar
- [x] main.css tokens + base + components
- [x] customizer + template-tags + breadcrumbs
- [x] PHP 7.4 + 8.2 lint clean, theme activates, no fatals with/without plugin
- [x] Docker वर verified: header/footer/mobile nav (Esc + focus return fixed), breadcrumbs, WhatsApp/Call buttons, demo content + menus render, debug.log clean, console errors नाहीत

### Phase 5 — Theme pages `[x]` (design image वरून — 2026-09-14 share केलेला mockup: Home/About/Listing/Details/Contact/Mobile)
- [x] front-page + sections (hero+search, trust, featured, requirement, CTA)
- [x] archive + filter sidebar/drawer + load more (purpose tabs, ptype/config/location checkboxes, budget radios, sort, AJAX load more, desktop auto-submit)
- [x] single property + gallery (thumb swap, keyboard) + sticky enquiry card (Send on WhatsApp + Call Now)
- [x] about-us (hero+content, stats, approach, why) + contact-us (info cards + form + map)
- [x] index, page, search, 404, comments, sidebar, searchform (functional baseline, not individually design-matched — no design screen given for these)
- [x] taxonomy-property_location (reuses archive-property.php)
- [x] woocommerce wrapper (Phase 4 मध्येच झाला होता, guarded)
- [x] main.js modules: nav toggle, purpose→budget optgroup swap, filter drawer + auto-submit, sort auto-submit, AJAX load more, gallery keyboard nav
- [ ] editor-style.css आहे (Phase 4), screenshot.png अजून नाही (visual only, blocking नाही)
- [x] PHP 7.4+8.2 lint clean; Docker वर verified: home/about/listing/details/contact सगळे design सोबत जुळले, filters (checkbox+radio+purpose) काम करतात, enquiry submit → Mailpit मध्ये mail + success notice, mobile (375px) hero/filters-drawer तपासले, debug.log रिकामा, fresh-tab console क्लीन
- [x] Bug fixes during testing: mobile drawer Esc था bug (Phase 4 पासून), single-property location line मध्ये चुकीचा `bloginfo('name')`, enquiry phone `pattern` regex मधले unescaped `()` (नवीन ब्राउझर्समध्ये console error — theme + plugin दोन्ही fallback templates मध्ये fix केला)

### Phase 6 — Child theme + i18n + Docs `[x]`
- [x] child theme (`style.css` Template header, `functions.php` parent-dependent enqueue, README) — Docker वर activate करून तपासले: renders fine, console/debug.log clean, नंतर parent theme परत active केला
- [x] `.pot` files (wp-cli make-pot) — plugin 333 strings, theme 182 strings
- [x] root `README.md` (10 sections: overview, requirements, install, broker guide, customizer map, shortcodes, developer notes, compatibility, uninstall/data safety, Docker dev)

### Phase 7 — QA `[x]`
- [x] `php -l` on PHP 7.4 + 8.2 — clean
- [x] Plugin OFF → सगळे pages 200 OK, no fatal (2 bugs fix केले: `spr_get_business('name')` in section-why, `spr_plain_text()` in breadcrumbs — आता guarded)
- [x] Plugin ON → menus, permalinks, demo content सगळे render
- [x] Filters (location, purpose, type, budget, sort) → योग्य results
- [x] AJAX Load More → success + bad nonce 403
- [x] Enquiry submit → Mailpit mail delivered, enquiry CPT saved, 302 redirect with `spr_status=sent`
- [x] JSON-LD: `RealEstateListing` on single, `RealEstateAgent` on home
- [x] Responsive: mobile (375×812) home/archive/single/about/contact — no overflow/overlap, sticky bar visible
- [x] One H1 per page (home, archive, single, about, contact, search)
- [x] Security grep: no unescaped echo with user input, no raw superglobals without sanitize, no `$wpdb`/`eval`/`extract`/deprecated, all mutation forms nonce'd
- [x] Console errors: zero; debug.log: empty
- [x] CF7 install → shortcode Customizer मधून set → contact page वर CF7 form render, 200 OK, debug.log clean
- [x] Yoast SEO → no duplicate schema (Yoast: WebPage/BreadcrumbList/WebSite, our: RealEstateListing + RealEstateAgent — separate blocks), breadcrumbs Yoast ला defer होतात (`yoast_breadcrumb()`)
- [x] WooCommerce → shop 200, product 200, header/footer present, debug.log clean
- [x] WP Super Cache (WP_CACHE=true) → contact form nonce present, AJAX load more success — forms/AJAX cache-compatible
- [x] Cleanup: test plugins deactivated + deleted, test product deleted, debug.log clean

---

## 10. Security Checklist (प्रत्येक file लिहिताना)
- Input: `wp_unslash` → sanitize (`sanitize_text_field`, `sanitize_textarea_field`, `sanitize_email`, `sanitize_key`, `absint`, `esc_url_raw`, `sanitize_hex_color`, allow-lists).
- Output: `esc_html`, `esc_attr`, `esc_url`, `esc_textarea`, `wp_kses_post`; translations `esc_html__/esc_attr__/esc_html_e`.
- प्रत्येक form/meta save/ajax: nonce + capability (public forms सोडून).
- Direct DB नाही; `WP_Query/get_posts/get_terms/meta API`.
- No `eval`, `extract`, `create_function`, sessions, hardcoded `<script>/<link>`.
- Redirects फक्त `wp_safe_redirect` + `exit`.
- Uploads फक्त Media Library मधून (attachment IDs).

## 11. Caching / Compatibility Notes
- Page cache मुळे nonce stale होऊ शकतो → enquiry form: nonce fail झाल्यास friendly "please retry" + README मध्ये cache lifetime < 12h किंवा contact/single page exclude ची सूचना. Load More: nonce fail वर JS page reload fallback.
- SEO plugin active असल्यास theme कोणतेही meta/OG tags output करत नाही (फक्त `title-tag` core).
- WPML/Polylang: सगळे strings translatable, CPT/taxonomy `wpml-config.xml` (plugin मध्ये) — custom fields translate/copy settings.

## 12. Risks / निर्णय जे नंतर लागतील
| मुद्दा | सध्याचा निर्णय |
|---|---|
| Design image अजून नाही | Phase 1–4 आधी, Phase 5 image मिळाल्यावर |
| Fonts download (Google Fonts woff2) | build वेळी user परवानगी घेऊन download; नाही तर system font stack fallback |
| Real property photos | demo placeholders; broker स्वतः upload करेल |
| Live hosting/deploy | या plan मध्ये नाही — नंतर user सांगेल |

---

## 13. पुढच्या session मध्ये कसे सुरू करायचे
1. `D:\website projects\sp\PLAN.md` वाचा.
2. Section 9 मध्ये पहिला `[ ]` phase शोधा → तिथून काम सुरू.
3. Naming (Section 2), data model (Section 3), API (Section 4) बदलायचे असल्यास आधी PLAN.md update करा.
4. User ला reply मराठीत, थोडक्यात.
