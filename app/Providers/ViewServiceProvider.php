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
        // Inject $siteSetting variable into the public layout and pages
        View::composer(['components.layouts.public', 'pages.*'], function (\Illuminate\View\View $view) {
            static $siteSetting = false;
            if ($siteSetting === false) {
                $siteSetting = SiteSetting::query()->first();
            }
            $view->with('siteSetting', $siteSetting);
        });
    }
}
