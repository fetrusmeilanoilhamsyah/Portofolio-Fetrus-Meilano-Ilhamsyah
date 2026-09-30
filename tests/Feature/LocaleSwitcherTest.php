<?php

namespace Tests\Feature;

use App\Support\LocaleSwitcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LocaleSwitcherTest extends TestCase
{
    public function test_switches_id_to_en_and_keeps_query_string()
    {
        Route::get('/projects/{slug}', function () {
            return 'project';
        })->name('projects.show');
        Route::get('/en/projects/{slug}', function () {
            return 'project en';
        })->name('en.projects.show');

        // Simulasi request ID dengan query string
        $request = Request::create('/projects/abc?x=1', 'GET');
        $request->setRouteResolver(function () use ($request) {
            $route = Route::getRoutes()->match($request);

            return $route->bind($request);
        });

        $this->app->setLocale('id');

        $url = LocaleSwitcher::switchUrl($request);
        $this->assertStringContainsString('/en/projects/abc?x=1', $url);
    }

    public function test_switches_en_to_id_and_keeps_query_string()
    {
        // Simulasi request EN
        $request = Request::create('/en/projects/abc?y=2', 'GET');
        $request->setRouteResolver(function () use ($request) {
            $route = Route::getRoutes()->match($request);

            return $route->bind($request);
        });

        $this->app->setLocale('en');

        $url = LocaleSwitcher::switchUrl($request);
        $this->assertStringContainsString('/projects/abc?y=2', $url);
    }

    public function test_handles_home_route_from_en_to_id()
    {
        $request = Request::create('/en', 'GET');
        $request->setRouteResolver(function () use ($request) {
            $route = Route::getRoutes()->match($request);

            return $route->bind($request);
        });

        $this->app->setLocale('en');

        $url = LocaleSwitcher::switchUrl($request);
        // /en to / should be the base URL
        $this->assertEquals(url('/'), $url);
    }

    public function test_handles_home_route_from_id_to_en()
    {
        $request = Request::create('/', 'GET');
        $request->setRouteResolver(function () use ($request) {
            $route = Route::getRoutes()->match($request);

            return $route->bind($request);
        });

        $this->app->setLocale('id');

        $url = LocaleSwitcher::switchUrl($request);
        $this->assertEquals(url('/en'), $url);
    }
}
