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

## 4. Deployment (cPanel Git Version Control)

The repo ships a `.cpanel.yml` at its root, so cPanel does the work. Pushing to
GitHub is **not** enough on its own — you must press **Deploy** in cPanel (or
automate it, see below).

### How it works

`.cpanel.yml` lays the app out like this:

```
~/sprealtors     ← application code (app/, config/, vendor/, storage/ …)
~/public_html    ← contents of public/ (the web root)
```

On each deploy it copies the code across, repoints `index.php` at
`~/sprealtors`, installs composer dependencies, runs migrations, links
storage and rebuilds the config/route/view caches.

### One-time server setup

Do this once, before the first deploy:

1. **cPanel → Git™ Version Control → Create.** Clone
   `https://github.com/Sachinx1911/SP-REALTORS-.git` into e.g. `~/repositories/sprealtors`.
2. **Create the app directory:** `~/sprealtors`
3. **Upload the production `.env`** to `~/sprealtors/.env`. It is deliberately
   not in git. Use the values below.
4. **Dependencies.** If your host has `composer` on PATH, `.cpanel.yml` installs
   them for you. If not, run `composer install --no-dev --optimize-autoloader`
   locally and upload the resulting `vendor/` folder to `~/sprealtors/vendor`
   once — later deploys reuse it.
5. **Create the database** in cPanel → MySQL Databases, and put its name/user/
   password in the `.env`.
6. **Set PHP 8.3+** in cPanel → MultiPHP Manager, with `pdo_mysql`, `mbstring`,
   `openssl`, `gd` (WebP), `zip`, `exif`, `fileinfo` enabled.

### Production `.env`

```
APP_NAME="SP REALTORS"
APP_ENV=production
APP_DEBUG=false
APP_KEY=                      # generate: php artisan key:generate
APP_URL=https://sprealtors.in

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true
```

### Deploying a change

```bash
npm run build          # compile assets — the server has no Node
git add -A
git commit -m "..."
git push origin main
```

Then in **cPanel → Git Version Control → Manage → Pull or Deploy → Deploy HEAD Commit**.

> **Always run `npm run build` and commit the result before pushing.**
> `public/build/` is committed on purpose. If you skip it, the live site loads
> with no styling because cPanel cannot compile assets.

### Automating the deploy (optional)

To deploy on every push instead of clicking Deploy, add a GitHub webhook
pointing at your cPanel deploy endpoint, or add a cron job on the server:

```bash
cd ~/repositories/sprealtors && git pull origin main && /usr/local/cpanel/bin/cpanel-git-deploy
```

### First deploy checklist

After the first successful deploy, verify:

- `https://sprealtors.in` loads **with styling** (if unstyled, `public/build` is missing)
- `https://sprealtors.in/admin` shows the login page
- Log in, then change the seeded admin password immediately
- An uploaded property image displays (confirms the `storage` symlink)
- `https://sprealtors.in/sitemap.xml` returns XML — submit it in Search Console

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
- `is_admin` is not mass-assignable — it can only be set explicitly
- Upload validation: MIME type + extension + 5 MB limit, re-encoded through GD
  (which strips any payload hidden inside an image)
- Honeypot + rate limiting (10/min) on the public enquiry form; 5/min on login
- Eloquent/query builder throughout; the one `selectRaw` column is allow-listed
- Blade auto-escaping on all output
- Unpublished properties/projects return 404 and never appear in listings
- Security headers on every response (`SecurityHeaders` middleware): CSP,
  `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`,
  `Permissions-Policy`, and HSTS once served over HTTPS
- Version banners suppressed (`expose_php=Off`, `ServerTokens Prod`)
- `public/.htaccess` denies dotfiles and `.env`/`.log`/`.sql`-type files

### Production checklist

Set these in `.env` before going live:

```
APP_ENV=production
APP_DEBUG=false              # never true in production — leaks stack traces
SESSION_SECURE_COOKIE=true   # requires HTTPS
SESSION_ENCRYPT=true
```

Then change the seeded admin password and confirm `/admin` requires login.

> On cPanel you may not control Apache's main config. If `Server:` still shows a
> full version string, ask the host to set `ServerTokens Prod`. This is
> information disclosure only, not a vulnerability by itself.

---

## 8. SEO

- Per-page title, meta description, canonical, Open Graph and Twitter tags
- **`sitemap.xml`** generated from the database at `/sitemap.xml` — all published
  properties, projects and per-location landing pages, with `lastmod` taken from
  each record's `updated_at`
- **`robots.txt`** disallows `/admin` and faceted filter URLs, and points to the sitemap
- **Canonical strategy:** filtered listing pages are `noindex, follow` and
  canonicalise to the clean listing URL, so filter combinations never create
  duplicate content; paginated pages self-canonicalise so deep listings stay
  indexable
- **Schema.org:** `RealEstateAgent` + `WebSite` (with SearchAction) site-wide,
  `RealEstateListing` on properties, `FAQPage` on contact, `BreadcrumbList` on
  every breadcrumb trail — all validated as parsing JSON
- Clean URLs, semantic HTML, one `<h1>` per page, alt text on images
- WebP images with JPEG fallback; long-lived cache headers and gzip via `.htaccess`
- Admin can override SEO title/description per property, project and location

After deploying, submit `https://sprealtors.in/sitemap.xml` in
[Google Search Console](https://search.google.com/search-console) so Google
starts indexing.
