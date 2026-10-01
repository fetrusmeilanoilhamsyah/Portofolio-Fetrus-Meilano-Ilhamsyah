<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class StageNineTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_palette_endpoint_returns_json()
    {
        Project::factory()->published()->create([
            'title' => 'Proyek Spesial',
        ]);

        $response = $this->get('/api/command-palette');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Proyek Spesial']);
        $response->assertJsonFragment(['title' => __('ui.home')]);
    }

    public function test_cache_is_cleared_on_content_change()
    {
        $version = Cache::get('guest_cache_version', 1);

        $project = Project::factory()->published()->create();

        $newVersion = Cache::get('guest_cache_version', 1);
        $this->assertGreaterThan($version, $newVersion);
    }

    public function test_guest_cache_middleware_caches_public_routes()
    {
        Cache::flush(); // Prevent state leakage
        config(['app.debug' => false]); // Disable debug mode to test caching

        // Panggil route sitemap
        $response1 = $this->get('/sitemap.xml');
        $response1->assertStatus(200);

        $version = Cache::get('guest_cache_version', 1);
        $cacheKey = 'guest_response_'.$version.'_'.sha1(url('/sitemap.xml'));

        $this->assertTrue(Cache::has($cacheKey));

        // Panggil route sebagai admin (tidak boleh dicache)
        $user = User::factory()->create();
        $response2 = $this->actingAs($user)->get('/projects');

        $cacheKey2 = 'guest_response_'.$version.'_'.sha1(url('/projects'));
        $this->assertFalse(Cache::has($cacheKey2));
    }

    public function test_security_headers_are_present()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_markdown_renders_safely()
    {
        $unsafeMarkdown = "Ini [link aman](https://google.com) dan ini [link tidak aman](javascript:alert('xss')). <script>alert('xss')</script>";

        $project = Project::factory()->published()->create([
            'body' => $unsafeMarkdown,
        ]);

        $response = $this->get('/projects/'.$project->slug);
        $response->assertStatus(200);

        // Script tag harus distrip
        $response->assertDontSee("<script>alert('xss')</script>", false);
        // Link tidak aman harus dirender sebagai teks biasa atau diabaikan sesuai konfigurasi CommonMark
        $response->assertDontSee('href="javascript:alert', false);
    }
}
