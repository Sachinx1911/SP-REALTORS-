# SP REALTORS — Custom Real Estate Website

Custom-built real estate website for **SP REALTORS** (sprealtors.in), Navi Mumbai.
_Properties · People · Possibilities_

No WordPress. No page builders. Laravel 12 + Tailwind CSS + MySQL 8, with a custom admin panel.

---

## 1. Tech stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.3+ |
| Database | MySQL 8 |
| Frontend | Blade, HTML5, Tailwind CSS v4, vanilla JavaScript |
| Icons | Inline SVG (Lucide-style) — no icon library loaded |
| Images | WebP conversion + resize on upload (GD) |
| Auth | Laravel session auth, admin-only gate |
| Hosting | cPanel / shared hosting compatible (plain PHP + MySQL at runtime) |

Node is used **only at build time** to compile assets. Production runs on PHP + MySQL alone.

---

## 2. Pages

**Public**

| URL | Page |
|---|---|
| `/` | Home |
| `/properties` | Properties listing (filters, sort, pagination) |
| `/property/{slug}` | Property details |
| `/projects` | Projects listing |
| `/project/{slug}` | Project details |
| `/about-us` | About Us |
| `/contact-us` | Contact (form + FAQ + map) |

**Admin** (`/admin`, login required)

Dashboard · Properties · Projects · Locations · Testimonials · Enquiries · Settings

---

## 3. Local development (Docker)

```bash
cd sprealtors-app
docker compose up -d --build
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
npm install && npm run build
```

- Site: http://localhost:8000
- Admin: http://localhost:8000/admin — `admin@sprealtors.in` / `password`

> Change the admin password immediately in any real environment.

While working on the frontend, use `npm run dev` for hot reload instead of `npm run build`.

---

## 4. Production deployment (cPanel / shared hosting)

Laravel expects the web root to be `public/`. On shared hosting you usually cannot move the
document root, so use this layout:

```
/home/USER/
├── sprealtors/          ← the whole Laravel app (everything except public/)
└── public_html/         ← contents of the app's public/ folder
```

**Steps**

1. **Build assets locally** (the server does not need Node):
   ```bash
   npm install && npm run build
   ```
   Upload the generated `public/build/` directory with the rest of `public/`.
   It is gitignored by default, so copy it across manually or add a deploy step for it.

2. **Upload** the project. Put the application in `~/sprealtors/` and the contents of
   `public/` into `~/public_html/`.

3. **Point `public_html/index.php` at the app** — edit the two require paths:
   ```php
   require __DIR__.'/../sprealtors/vendor/autoload.php';
   $app = require_once __DIR__.'/../sprealtors/bootstrap/app.php';
   ```

4. **Install dependencies** (SSH, or upload a locally-built `vendor/`):
   ```bash
   cd ~/sprealtors
   composer install --no-dev --optimize-autoloader
   ```

5. **Environment** — copy `.env.example` to `.env` and set:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://sprealtors.in
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_DATABASE=your_db
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```
   Then:
   ```bash
   php artisan key:generate
   ```

6. **Database**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force      # optional: demo content + admin user
   ```

7. **Storage symlink** — uploaded images live in `storage/app/public`:
   ```bash
   php artisan storage:link
   ```
   If the symlink cannot be created on your host, create it manually so that
   `public_html/storage` → `~/sprealtors/storage/app/public`.

8. **Cache for production**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   Re-run these after any `.env` or code change.

9. **Permissions**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

10. **PHP version** — set PHP 8.3+ in cPanel, with extensions:
    `pdo_mysql`, `mbstring`, `openssl`, `gd` (WebP enabled), `zip`, `exif`, `fileinfo`.

---

## 5. Admin guide

**Log in:** `/admin`

| Section | What you can do |
|---|---|
| **Properties** | Add/edit/delete listings. Set price, area, beds/baths, furnishing, amenities, highlights, gallery, RERA, map. Toggle **Published** and **Featured** (featured shows on the home page). |
| **Projects** | Same as properties, plus developer, starting price, configuration rows, possession, brochure PDF, floor plans and nearby places. |
| **Locations** | The areas you serve. These drive the "Areas We Serve" section and the property/project location filters. Upload an image per location. |
| **Testimonials** | Client reviews shown on the home page. |
| **Enquiries** | Every form submission is stored here. Filter by status/source, track each lead as New → Contacted → Qualified → Closed, and add internal notes. |
| **Settings** | Phone, WhatsApp, email, address, working hours, map URL, social links, About-page statistics and default SEO text. Changed in one place, used everywhere. |

**Price entry:** enter the full rupee amount (e.g. `8500000`). The site formats it automatically
as `₹ 85 Lac` / `₹ 1.20 Cr`. For rentals tick **Monthly Rent** to display `₹ 55,000 / month`.

**Highlights / nearby places:** one item per line in the textarea.

**Images:** JPG/PNG/WebP/AVIF up to 5 MB. Uploads are auto-resized (max 1600px) and converted
to WebP to keep pages fast.

---

## 6. Architecture

```
app/
├── Http/
│   ├── Controllers/          HomeController, PropertyController, ProjectController,
│   │   └── Admin/            PageController, ContactController, EnquiryController
│   ├── Requests/             PropertyRequest, ProjectRequest, StoreEnquiryRequest
│   └── Middleware/           EnsureUserIsAdmin
├── Models/                   Property, PropertyImage, Project, ProjectImage,
│                             Location, Testimonial, Enquiry, Setting, User
└── Support/                  Amenities, ImageUploader, helpers.php

resources/views/
├── components/               Reusable Blade components
│   ├── layouts/              site, admin
│   ├── site/                 header, footer
│   └── admin/                field, status-badge
├── pages/                    home, about, contact, properties/, projects/
├── admin/                    dashboard, properties/, projects/, locations/,
│                             testimonials/, enquiries/, settings
└── pagination/default.blade.php
```

**Design system** lives in `resources/css/app.css` as Tailwind `@theme` tokens — brand colours,
fonts, radii, shadows. Change a token there and it updates site-wide.

---

## 7. Security

- CSRF protection on every form
- Form Request validation on all writes
- Admin routes behind `auth` + `admin` middleware; no public registration
- Upload validation: MIME type + extension + 5 MB limit, re-encoded through GD
- Honeypot + rate limiting (10/min) on the public enquiry form; 5/min on login
- Eloquent/query builder throughout (no raw user-interpolated SQL)
- Blade auto-escaping on all output

---

## 8. SEO

- Per-page title, meta description, canonical, Open Graph and Twitter tags
- `RealEstateAgent` schema site-wide; `RealEstateListing` on property pages
- `BreadcrumbList` schema on every breadcrumb trail
- Clean URLs, semantic HTML, one `<h1>` per page, alt text on images
- Admin can override SEO title/description per property, project and location
