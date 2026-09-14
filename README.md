# SP REALTORS — WordPress Site

Production WordPress site for **SP REALTORS** (sprealtors.in), a Navi Mumbai real-estate broker.
_Properties · People · Possibilities_

Two custom packages ship together:

| Package | Role |
|---|---|
| `sp-realtors-core/` (plugin) | All **data & functionality**: properties, locations, testimonials, enquiries, forms, shortcodes, structured data. |
| `sp-realtors/` (theme) | **Design & presentation** only. Renders the plugin's data. |
| `sp-realtors-child/` (child theme) | Safe place for the broker's own CSS/template tweaks. |

## 1. Why a plugin *and* a theme?

The WordPress Theme Handbook draws a hard line: anything that is **content or functionality** belongs in a plugin, not a theme — themes are only supposed to handle presentation. We follow that rule strictly here:

- Switch the theme (or deactivate it entirely) → every property, location, testimonial and enquiry **stays intact**, because none of it lives in theme code or theme-only tables.
- The theme checks `sp_realtors_core_active()` before rendering any property/enquiry UI. If the plugin is off, those sections hide gracefully — pages never fatal, they just show without listings ([inc/plugin-notice.php](sp-realtors/inc/plugin-notice.php)).
- The plugin never assumes *this* theme is active — it ships its own fallback templates (`sp-realtors-core/templates/`) so it still renders something sane under any theme.

## 2. Requirements

- WordPress 6.3+
- PHP 7.4+ (code is written to be 7.4-compatible; tested through 8.2)
- MySQL 5.7+ / MariaDB equivalent
- Pretty permalinks (`/%postname%/`) — required for `/properties/{slug}/` and `/location/{slug}/` URLs

## 3. Installation (production)

1. Upload & activate the plugin: `sp-realtors-core` → **Plugins**.
2. Upload & activate the theme: `sp-realtors` (or the child theme `sp-realtors-child` if you want your own CSS layer) → **Appearance → Themes**.
3. **Settings → Permalinks** → Save (flushes rewrite rules so property/location URLs work).
4. **Settings → SP Realtors** → set the enquiry notification email, review the schema/uninstall toggles.
5. **Customize → Business Info** → enter the real phone, WhatsApp number (digits only, country code, e.g. `919800000000`), email, address, hours, social links.
6. Optional: **Settings → SP Realtors → Demo Content → Import** to seed 6 sample properties, 3 testimonials, and Home/About/Contact pages — useful for a first look, or to see the expected content shape. Everything demo-imported is tagged internally, so **Remove demo content** only trashes those items (never a permanent delete).
7. Add real properties: **Properties → Add New**. Fill the Property Details meta box tabs (Pricing, Specs, Location & Map, Gallery, Features, Legal) and set Location/Type/Purpose/Configuration in the sidebar.

## 4. For the broker (day-to-day, no code needed)

- **नवीन property टाकायची:** wp-admin → Properties → Add New. Title, description (main editor), Featured Image, आणि उजवीकडे Location/Type/Buy-Rent/Configuration निवडा. खाली "Property Details" box मध्ये price, area, bedrooms, gallery photos, amenities, map link भरा. "★ Featured" checkbox केल्यास ती property Home page वर दिसेल.
- **Testimonial टाकायची:** Testimonials → Add New. Title = client चं नाव, मुख्य content = quote, Featured Image = फोटो, rating आणि "bought/rented what" detail sidebar मध्ये.
- **Enquiries बघायच्या:** Properties मेनू खाली "Enquiries" — फॉर्मवरून आलेली प्रत्येक चौकशी इथे सेव्ह होते (mail पाठवणं fail झालं तरी).
- **Phone/WhatsApp/Address बदलायचा:** Appearance → Customize → Business Info.
- **Home page चे मजकूर/photos बदलायचे:** Appearance → Customize → त्या-त्या section च्या panel मध्ये (Hero, Trust Strip, Browse by Requirement, Why Choose Us, CTA Banner, About Page, Footer, Colors).

## 5. Customizer map (panel: "SP Realtors Theme")

| Section | Controls |
|---|---|
| Business Info | phone, WhatsApp, email, address, hours, social URLs *(registered by the plugin — survives theme switches)* |
| Hero | eyebrow, heading, subtext, image |
| Trust Strip | 4 × icon/title/text |
| Browse by Requirement | 4 × title/text/image/URL |
| Why Choose Us | icon/title/text repeater |
| CTA Banner | heading, text, button label |
| About Page | stats (number/label ×4), approach steps (title/text ×4) |
| Footer | tagline, copyright text |
| Colors | navy / blue / green / gold |
| Mobile | sticky Call/WhatsApp bar on/off |
| Contact Page | form shortcode override, map embed URL |

## 6. Shortcodes

| Shortcode | Use |
|---|---|
| `[sp_featured_properties count="3"]` | Featured property cards |
| `[sp_properties location="kharghar" purpose="buy" count="6"]` | Filtered property grid |
| `[sp_property_search]` | Standalone search/filter form |
| `[sp_enquiry_form property_id=""]` | Enquiry/contact form |
| `[sp_testimonials count="3"]` | Testimonial cards |
| `[sp_business_info field="phone"]` | Prints one business-info field (`phone`, `whatsapp`, `email`, `address`, `hours`, or a social key) |

These work on any page/post, in any active theme.

## 7. Developer notes

- **Naming:** plugin functions/constants use `spr_` / `SPR_*`; theme uses `sp_realtors_` / `SP_REALTORS_*`. Meta keys are `_spr_*` (underscore-prefixed → hidden from the default Custom Fields box).
- **Public API:** the theme talks to the plugin *only* through [`includes/helpers.php`](sp-realtors-core/includes/helpers.php) (`spr_get_property()`, `spr_format_price()`, `spr_get_featured_properties()`, `spr_whatsapp_url()`, etc.). Don't reach into plugin internals or query meta keys directly from theme code — go through the helper functions so the plugin's internal storage can change without breaking the theme.
- **Template overrides:** the plugin's own fallback templates (`sp-realtors-core/templates/`) are used only when the active theme doesn't provide a matching file. This theme overrides all of them (`archive-property.php`, `single-property.php`, `template-parts/card-property.php`, `template-parts/enquiry-form.php`, `template-parts/search-form.php`, `template-parts/content-none.php`). To customize further without touching the theme itself, copy any of these into `sp-realtors-child/` at the same relative path.
- **Hook:** `do_action( 'spr_enquiry_received', $data, $enquiry_id )` fires after every enquiry is saved — hook here for CRM/webhook integrations without editing plugin code.
- **Child theme:** see [`sp-realtors-child/README.md`](sp-realtors-child/README.md).
- **Security baseline:** every form/meta-save/AJAX handler is nonce + capability checked; all output is escaped (`esc_html/esc_attr/esc_url/wp_kses_post`); all input is sanitized before storage; no direct `$wpdb` queries; redirects only via `wp_safe_redirect()`.
- **i18n:** both packages are translation-ready (`sp-realtors-core.pot`, `sp-realtors.pot` in their respective `languages/` folders). Regenerate after copy changes:
  ```bash
  wp i18n make-pot sp-realtors-core sp-realtors-core/languages/sp-realtors-core.pot --domain=sp-realtors-core --exclude=assets/demo
  wp i18n make-pot sp-realtors sp-realtors/languages/sp-realtors.pot --domain=sp-realtors
  ```

## 8. Compatibility notes

- **Caching (WP Super Cache / W3TC / etc.):** enquiry and Load-More nonces are stored in cached HTML, so they can go stale on very long cache lifetimes. Either exclude the Contact page and property archive/single pages from full-page cache, or keep cache lifetime under ~12h. Both the enquiry form and Load More fail safely on a stale nonce (friendly retry message / page-reload fallback) rather than fataling.
- **SEO plugins (Yoast / Rank Math):** the theme adds no meta/OG tags of its own beyond core's `title-tag` support, so there's no duplication. Breadcrumbs prefer the SEO plugin's own function (`yoast_breadcrumb()` / `rank_math_the_breadcrumbs()`) when active. Structured data (`RealEstateListing` / `RealEstateAgent`) can be turned off entirely in **Settings → SP Realtors**, and auto-suppresses its LocalBusiness output if Yoast Local SEO or Rank Math's Local module is active, to avoid duplicate schema.
- **WooCommerce:** guarded — `inc/woocommerce.php` and `woocommerce.php` only load if WooCommerce is active; nothing breaks if it isn't.
- **WPML / Polylang:** all strings are translatable; `wpml-config.xml` ships in the plugin for custom-field/taxonomy translation config.

## 9. Uninstall & data safety

- Deactivating the plugin or switching the theme **never deletes data**.
- Uninstalling the plugin (delete via Plugins screen) only removes properties/testimonials/enquiries/options if **Settings → SP Realtors → "Delete data on uninstall"** was explicitly turned on beforehand. Default is off.
- Demo content removal only trashes items tagged as demo — never a hard delete.

## 10. Local development (Docker)

```bash
docker compose up -d
```

- Site: http://localhost:8080 · phpMyAdmin: http://localhost:8081 · Mailpit (catches all outgoing mail): http://localhost:8025
- WP-CLI: `docker compose exec -T -e HTTP_HOST=localhost:8080 wpcli wp <command>` (add `--user=1` for admin-capability tasks)
- Plugin smoke tests: `... wpcli wp eval-file /tests/plugin-smoke.php --user=1`
- PHP 7.4 lint: `docker run --rm -v "${PWD}:/app" -w /app php:7.4-cli sh docker/tools/lint.sh`
- After a fresh volume, pretty permalinks need the `.htaccess` copied in: `docker compose cp docker/config/htaccess wordpress:/var/www/html/.htaccess`

`docker-compose.yml` and everything under `docker/` are dev-only and are not part of the production deploy.

See [`PLAN.md`](PLAN.md) for the full build plan, data model, and phase-by-phase progress.
