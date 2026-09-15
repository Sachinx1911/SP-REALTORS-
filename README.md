# SP REALTORS

Custom real estate website for **SP REALTORS** (sprealtors.in), Navi Mumbai.
_Properties · People · Possibilities_

## Repository layout

| Folder | Contents |
|---|---|
| [`sprealtors-app/`](sprealtors-app/) | The website — Laravel 12 + Tailwind CSS + MySQL 8, with a custom admin panel |
| `design/` | Approved design reference images |

## Getting started

Everything lives in `sprealtors-app/`. See its
[README](sprealtors-app/README.md) for local setup, the admin guide and
cPanel deployment instructions.

```bash
cd sprealtors-app
docker compose up -d --build
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
npm install && npm run build
```

- Site: http://localhost:8000
- Admin: http://localhost:8000/admin

## Stack

Laravel 12 · PHP 8.3+ · MySQL 8 · Tailwind CSS v4 · vanilla JavaScript.
Runs on plain PHP + MySQL shared hosting (cPanel) — no Node required at runtime.
