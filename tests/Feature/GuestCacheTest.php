<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GuestCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat temporary directory untuk file cache test
        $tmpCachePath = storage_path('framework/cache/test_guest_cache');
        if (! File::exists($tmpCachePath)) {
            File::makeDirectory($tmpCachePath, 0755, true);
        }

        // Force konfigurasi menggunakan file driver dan mematikan debug
        Config::set('cache.default', 'file');
        Config::set('cache.stores.file.path', $tmpCachePath);
        Config::set('app.debug', false);

        Cache::flush();
    }

    protected function tearDown(): void
    {
        Cache::flush();
        $tmpCachePath = storage_path('framework/cache/test_guest_cache');
        if (File::exists($tmpCachePath)) {
            File::deleteDirectory($tmpCachePath);
        }
        parent::tearDown();
    }

    public function test_guest_cache_miss_then_hit()
    {
        // 1st request MISS
        $response1 = $this->get('/sitemap.xml');
        $response1->assertStatus(200);
        $response1->assertHeader('X-Cache', 'MISS');
        $response1->assertHeaderMissing('Set-Cookie');
        $response1->assertHeader('X-Content-Type-Options', 'nosniff');
        $response1->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response1->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response1->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), browsing-topics=()');
        $response1->assertHeader('Content-Security-Policy');

        // 2nd request HIT (same HTML, 200, no Set-Cookie)
        $response2 = $this->get('/sitemap.xml');
        $response2->assertStatus(200);
        $response2->assertHeader('X-Cache', 'HIT');
        $response2->assertHeaderMissing('Set-Cookie');
        $response2->assertHeader('X-Content-Type-Options', 'nosniff');
        $response2->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response2->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response2->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), browsing-topics=()');
        $response2->assertHeader('Content-Security-Policy');

        $this->assertEquals($response1->getContent(), $response2->getContent());
    }

    public function test_guest_cache_query_params()
    {
        // ?x=1 and ?x=2 use same cache key since only 'tab' is allowed in cache key
        $response1 = $this->get('/?x=1');
        $response1->assertHeader('X-Cache', 'MISS');

        $response2 = $this->get('/?x=2');
        $response2->assertHeader('X-Cache', 'HIT');
    }

    public function test_guest_cache_ignores_auth_and_admin()
    {
        // Logged in users never cached
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');
        $response->assertHeaderMissing('X-Cache');

        // /admin never cached
        $responseAdmin = $this->get('/admin/login');
        $responseAdmin->assertHeaderMissing('X-Cache');
    }

    public function test_content_changed_resets_cache()
    {
        $response1 = $this->get('/sitemap.xml');
        $response1->assertHeader('X-Cache', 'MISS');

        $response2 = $this->get('/sitemap.xml');
        $response2->assertHeader('X-Cache', 'HIT');

        // Simulate ContentChanged by making a project (this should trigger ClearGuestCache listener)
        Project::factory()->published()->create();

        $response3 = $this->get('/sitemap.xml');
        $response3->assertHeader('X-Cache', 'MISS');
    }
}
