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
        //
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
