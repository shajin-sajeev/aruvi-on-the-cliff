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

$app->handleRequest(\Illuminate\Http\Request::capture());
