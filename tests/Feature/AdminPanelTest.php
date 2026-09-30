<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use App\Services\ImageOptimizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_is_rejected()
    {
        Config::set('portfolio.admin_email', 'admin@example.com');
        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_panel()
    {
        Config::set('portfolio.admin_email', 'admin@example.com');
        $user = User::factory()->create(['email' => 'admin@example.com']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_slug_does_not_change_after_published()
    {
        $project = Project::factory()->create([
            'title' => ['id' => 'Judul Lama', 'en' => 'Old Title'],
            'status' => ProjectStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $originalSlug = $project->slug;

        // Coba ubah judul
        $project->update([
            'title' => ['id' => 'Judul Baru', 'en' => 'New Title'],
            'slug' => 'judul-baru', // Simulasi kalau dari form ada perubahan slug (walau disabled, misal di-bypass)
        ]);

        $this->assertEquals($originalSlug, $project->fresh()->slug);
    }

    public function test_image_optimizer_rejects_non_image()
    {
        $optimizer = new ImageOptimizer;

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File bukan gambar.');

        $optimizer->optimizeAndSave($file);
    }

    public function test_empty_title_id_does_not_result_in_empty_slug()
    {
        // Tes penjagaan generateUniqueSlug
        $slug = Project::generateUniqueSlug('');

        $this->assertNotEmpty($slug);
        $this->assertStringStartsWith('project-', $slug);
    }

    public function test_admin_can_access_profile_page()
    {
        Config::set('portfolio.admin_email', 'admin@example.com');
        $user = User::factory()->create(['email' => 'admin@example.com']);

        $response = $this->actingAs($user)->get('/admin/profile');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_profile_page()
    {
        Config::set('portfolio.admin_email', 'admin@example.com');
        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this->actingAs($user)->get('/admin/profile');

        $response->assertStatus(403);
    }
}
