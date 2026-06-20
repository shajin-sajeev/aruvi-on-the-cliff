# Aruvi on the Cliff

A premium Laravel resort management platform for a luxury beachside property. The site includes a dynamic public CMS, booking engine, admin dashboard, guest authentication, room availability checks, booking status workflow, invoice receipt view, restaurant menu, gallery, reviews, FAQs, policies, SEO/settings, and RBAC-ready user management.

## Stack

- PHP 8.3+
- Laravel framework installed in this workspace
- MySQL or SQLite for local development
- Bootstrap 5 plus custom teal/sea-green resort styling
- MVC architecture with Eloquent models, controllers, routes, migrations, seeders, and Blade views

## Installation

1. Install PHP dependencies:

```bash
composer install
```

2. Install frontend tooling if you plan to compile Vite assets:

```bash
npm install
npm run build
```

3. Configure `.env` database settings. For MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aruvi_on_the_cliff
DB_USERNAME=root
DB_PASSWORD=
```

4. Generate the app key if needed:

```bash
php artisan key:generate
```

5. Run migrations and seed the platform:

```bash
php artisan migrate:fresh --seed
```

6. Start the app:

```bash
php artisan serve
```

## Default Accounts

- Admin: `admin@aruvi.test` / `password`
- Guest: `guest@aruvi.test` / `password`

## Main URLs

- Website: `/`
- Rooms & Suites: `/rooms-suites`
- Booking: `/booking`
- Guest booking history: `/booking-history`
- Admin dashboard: `/admin`
- API rooms: `/api/rooms`
- API availability: `/api/availability?check_in=2026-07-01&check_out=2026-07-03`

## Admin Modules

The admin panel manages hero slides, homepage sections, rooms, room types, amenities, bookings, restaurant categories/items, gallery categories/items, attractions, reviews, testimonials, contact messages, FAQs, CMS pages, social links, site settings, SEO/payment keys, users, roles, and permissions.

## Booking Notes

The booking engine prevents double bookings by checking overlapping active bookings inside a database transaction with a row lock. Active blocking statuses are `pending`, `confirmed`, and `checked_in`. Booking statuses available to admins are `pending`, `confirmed`, `checked_in`, `checked_out`, and `cancelled`.

## Payment Gateway Readiness

Settings include placeholders for Razorpay, Stripe, and PayPal keys. Offline payment is enabled by default, so gateway SDK integration can be added without changing the booking domain model.

## Invoice Receipts

Each booking creates an invoice record and a print-friendly receipt screen. Use the browser print dialog to save it as PDF.

---

## Deploying to Vercel

### Prerequisites

- A [Vercel account](https://vercel.com)
- A hosted database (SQLite does **not** work on Vercel):
  - **MySQL** → [PlanetScale](https://planetscale.com) or [Railway](https://railway.app)
  - **PostgreSQL** → [Supabase](https://supabase.com) or [Neon](https://neon.tech)
- (Optional) [Upstash Redis](https://upstash.com) for persistent cache/sessions
- (Optional) AWS S3 or compatible for file uploads

### How it works

| File | Purpose |
|------|---------|
| `vercel.json` | Routes, build command, PHP serverless function config |
| `api/index.php` | Serverless entry point — bootstraps Laravel, maps `/tmp` for writable paths |
| `api/php.ini` | PHP extension + memory config for the Vercel runtime |
| `.vercelignore` | Files excluded from deployment |
| `.env.production` | Template for all environment variables you need to set in Vercel |

### Step-by-step

**1. Push your project to GitHub (or GitLab/Bitbucket).**

**2. Import the project in Vercel.**
Go to [vercel.com/new](https://vercel.com/new) → Import Git Repository → select your repo.

**3. Set environment variables in Vercel.**
Go to **Project → Settings → Environment Variables** and add every variable from `.env.production`:

| Variable | Value |
|----------|-------|
| `APP_KEY` | Run `php artisan key:generate --show` locally and paste the result |
| `APP_URL` | Your Vercel deployment URL, e.g. `https://aruvi-on-the-cliff.vercel.app` |
| `DB_CONNECTION` | `mysql` (or `pgsql`) |
| `DB_HOST` | Your hosted DB host |
| `DB_DATABASE` | Your database name |
| `DB_USERNAME` | DB username |
| `DB_PASSWORD` | DB password |
| `RAZORPAY_KEY_ID` | Your Razorpay key |
| `RAZORPAY_KEY_SECRET` | Your Razorpay secret |
| `SESSION_DRIVER` | `cookie` |
| `CACHE_STORE` | `array` |
| `QUEUE_CONNECTION` | `sync` |
| `FILESYSTEM_DISK` | `s3` (if using file uploads) |
| `AWS_*` | S3 credentials (if using file uploads) |

**4. Deploy.**
Vercel will run:
```
npm ci && npm run build && composer install --no-dev --optimize-autoloader --no-interaction
```

**5. Run migrations once after first deploy.**

Vercel doesn't support running artisan commands directly. Use one of these approaches:

**Option A — via your local machine pointing at the production DB:**
```bash
DB_CONNECTION=mysql DB_HOST=your-host DB_DATABASE=your-db \
  DB_USERNAME=your-user DB_PASSWORD=your-pass \
  php artisan migrate --force
```

**Option B — PlanetScale / Railway CLI** — connect to the DB and import the migration SQL.

**Option C** — add a temporary route that calls `Artisan::call('migrate', ['--force' => true])` (remove it immediately after).

### Important constraints on Vercel

| Feature | Serverless approach |
|---------|-------------------|
| File uploads | Must use S3 — local storage is wiped between requests |
| Sessions | `cookie` driver (stateless) or Redis via Upstash |
| Cache | `array` (per-request) or Redis via Upstash |
| Queue | `sync` (inline) or a managed queue service |
| Cron jobs | Use [Vercel Cron](https://vercel.com/docs/cron-jobs) calling an HTTP endpoint |
| SQLite | ❌ Not supported — use a hosted DB |
| `storage:link` | ❌ Not needed — serve uploads via S3 directly |

### Switching to Redis (recommended for production)

Install Predis:
```bash
composer require predis/predis
```

Then set in Vercel environment variables:
```
REDIS_CLIENT=predis
REDIS_URL=rediss://:password@your-upstash-host:6380
SESSION_DRIVER=redis
CACHE_STORE=redis
```
