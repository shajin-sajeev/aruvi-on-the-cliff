<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\ContactMessage;

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
        if (isset($_ENV['VIEW_COMPILED_PATH'])) {
            $this->app['config']->set(
                'view.compiled',
                $_ENV['VIEW_COMPILED_PATH']
            );
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        if (Schema::hasTable('settings')) {
            try {
                view()->share('settings', \App\Models\Setting::all()->pluck('value', 'key'));
            } catch (\Exception $e) {
                // Fallback in case of database connectivity issues during command executions
            }
        }

        if (Schema::hasTable('contact_messages')) {
            try {
                view()->composer('layouts.admin', function ($view) {
                    $view->with('unreadMessageCount', ContactMessage::where('status', 'new')->count());
                });
            } catch (\Exception $e) {
                // Ignore errors during bootstrap if database or migration not yet available.
            }
        }
    }
}
