<?php

/**
 * Vercel serverless entry point for Laravel.
 *
 * Vercel's filesystem is read-only at runtime (except /tmp).
 * This file bootstraps Laravel and rewrites paths that require
 * write access (views cache, logs, sessions) to /tmp.
 */

// ── Writable paths: redirect to /tmp ─────────────────────────────────────────
$tmpStorage = '/tmp/storage';

$writableDirs = [
    $tmpStorage,
    $tmpStorage . '/framework',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/logs',
    $tmpStorage . '/app',
    $tmpStorage . '/app/public',
];

foreach ($writableDirs as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

// ── Override storage paths before Laravel boots ───────────────────────────────
// These environment variables are read by Laravel's path helpers.
$_ENV['APP_STORAGE_PATH'] = $tmpStorage;

// ── Bootstrap Laravel ─────────────────────────────────────────────────────────
define('LARAVEL_START', microtime(true));

// Maintenance mode check (uses the real public path).
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Override storage_path() so compiled views / cache / logs go to /tmp.
$app->useStoragePath($tmpStorage);

// ── Run migrations and seed once per cold start ──────────────────────────────
// migrate runs every cold start (idempotent — skips already-run migrations).
// db:seed runs only if the settings table is empty (first deploy).
$migrationFlag = '/tmp/.migrated';
if (! file_exists($migrationFlag)) {
    try {
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->call('migrate', ['--force' => true]);
        file_put_contents($migrationFlag, date('Y-m-d H:i:s'));
    } catch (\Throwable $e) {
        error_log('Migration failed: ' . $e->getMessage());
    }
}

// Seed once: check if the settings table has any rows before seeding.
$seedFlag = '/tmp/.seeded';
if (! file_exists($seedFlag)) {
    try {
        $seeded = \Illuminate\Support\Facades\DB::table('settings')->exists();
        if (! $seeded) {
            $kernel = $kernel ?? $app->make(\Illuminate\Contracts\Console\Kernel::class);
            $kernel->call('db:seed', ['--force' => true]);
        }
        file_put_contents($seedFlag, date('Y-m-d H:i:s'));
    } catch (\Throwable $e) {
        error_log('Seeding failed: ' . $e->getMessage());
    }
}

$app->handleRequest(\Illuminate\Http\Request::capture());
