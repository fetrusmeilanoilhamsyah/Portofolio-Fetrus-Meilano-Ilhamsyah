<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Inject $siteSetting variable into the public layout
        View::composer('components.layouts.public', function (\Illuminate\View\View $view) {
            $view->with('siteSetting', SiteSetting::query()->first());
        });
    }
}
