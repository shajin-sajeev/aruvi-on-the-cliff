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
