<?php

namespace Tests\Feature\Pages;

use App\Enums\ExperienceKind;
use App\Enums\ProjectStatus;
use App\Models\Experience;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeAboutTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_published_featured_and_recent_projects_but_not_drafts()
    {
        // Settings
        SiteSetting::factory()->create([
            'name' => 'Fulan',
            'intro_home' => ['id' => 'Halo ini Fulan'],
        ]);

        // Projects
        $publishedFeatured = Project::factory()->create([
            'status' => ProjectStatus::Published,
            'is_featured' => true,
            'title' => ['id' => 'Proyek Unggulan 1'],
        ]);

        $publishedRecent = Project::factory()->create([
            'status' => ProjectStatus::Published,
            'is_featured' => false,
            'title' => ['id' => 'Proyek Terbaru 1'],
        ]);

        $draftProject = Project::factory()->create([
            'status' => ProjectStatus::Draft,
            'title' => ['id' => 'Proyek Draft'],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Halo ini Fulan');
        $response->assertSee('Proyek Unggulan 1');
        $response->assertSee('Proyek Terbaru 1');
        $response->assertDontSee('Proyek Draft');
    }

    public function test_about_shows_published_education_and_cv_button_only_if_cv_exists()
    {
        $setting = SiteSetting::factory()->create([
            'about_body' => ['id' => 'Ini halaman about.'],
            'cv_file' => 'cv/fulan.pdf',
            'skills' => [
                ['group' => 'Frontend', 'items' => 'HTML, CSS'],
            ],
        ]);

        $publishedEdu = Experience::factory()->create([
            'kind' => ExperienceKind::Pendidikan,
            'is_published' => true,
            'title' => ['id' => 'S1 Teknik Informatika'],
            'organization' => 'Universitas X',
        ]);

        $draftEdu = Experience::factory()->create([
            'kind' => ExperienceKind::Pendidikan,
            'is_published' => false,
            'title' => ['id' => 'SMA Y'],
            'organization' => 'Sekolah Z',
        ]);

        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('Ini halaman about.');
        $response->assertSee('Frontend');
        $response->assertSee('HTML');
        $response->assertSee('CSS');
        $response->assertSee('S1 Teknik Informatika');
        $response->assertSee('Universitas X');
        $response->assertDontSee('SMA Y');
        $response->assertSee('Unduh CV');
        $response->assertSee('cv/fulan.pdf');
    }

    public function test_about_hides_cv_button_if_no_file()
    {
        SiteSetting::factory()->create([
            'cv_file' => null,
            'about_body' => ['id' => 'Hai'],
        ]);

        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertDontSee('Unduh CV');
    }

    public function test_home_and_about_en_fallback()
    {
        SiteSetting::factory()->create([
            'name' => 'Fulan',
            // about_body.en tidak diisi, harus jatuh ke .id
            'about_body' => ['id' => 'Teks ID About', 'en' => ''],
            'intro_home' => ['id' => 'Teks ID Home', 'en' => ''],
        ]);

        $responseHome = $this->get('/en');
        $responseHome->assertStatus(200);
        $responseHome->assertSee('Teks ID Home');

        $responseAbout = $this->get('/en/about');
        $responseAbout->assertStatus(200);
        $responseAbout->assertSee('Teks ID About');
    }
}
