<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\ContactMessage;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // On Vercel the filesystem is read-only except /tmp.
        // The api/index.php entry point already calls $app->useStoragePath('/tmp/storage'),
        // but we also patch the compiled view path here for safety.
        $compiledPath = $_ENV['VIEW_COMPILED_PATH'] ?? getenv('VIEW_COMPILED_PATH') ?: null;

        if ($compiledPath) {
            $this->app['config']->set(
                'view.compiled',
                $compiledPath
            );
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        try {
            if (Schema::hasTable('settings')) {
                view()->share('settings', Setting::all()->pluck('value', 'key'));
            }
        } catch (\Throwable $e) {
            view()->share('settings', collect());
        }

        try {
            if (Schema::hasTable('contact_messages')) {
                view()->composer('layouts.admin', function ($view) {
                    $view->with('unreadMessageCount', ContactMessage::where('status', 'new')->count());
                });
            }
        } catch (\Throwable $e) {
            // Ignore bootstrap errors when the database is unavailable or not migrated yet.
        }
    }
}
