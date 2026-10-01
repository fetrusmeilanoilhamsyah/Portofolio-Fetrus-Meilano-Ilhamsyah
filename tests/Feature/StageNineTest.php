<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
