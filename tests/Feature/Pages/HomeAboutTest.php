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

    public function test_localized_routes_are_rendered_correctly()
    {
        $responseEn = $this->get('/en');
        $responseEn->assertSee('/en/about');
        $responseEn->assertSee('/en/projects');

        $responseId = $this->get('/');
        $responseId->assertSee('/about');
        $responseId->assertSee('/projects');
        $responseId->assertDontSee('/en/about');
    }

    public function test_project_links_use_localized_route()
    {
        Project::factory()->create([
            'status' => ProjectStatus::Published,
            'is_featured' => true,
            'slug' => 'proyek-1',
        ]);

        $responseEn = $this->get('/en');
        $responseEn->assertSee('/en/projects/proyek-1');
    }

    public function test_empty_state_about_is_shown_if_all_content_is_empty()
    {
        SiteSetting::factory()->create([
            'about_body' => null,
            'photo' => null,
            'skills' => null,
        ]);

        $response = $this->get('/about');
        $response->assertSee(__('ui.empty_coming_soon'));
    }

    public function test_placeholder_text_is_hidden()
    {
        SiteSetting::factory()->create([
            'name' => '[ISI: Nama Anda]',
            'role' => '[ISI: Peran Anda]',
            'intro_home' => ['id' => '[ISI: Perkenalan]'],
            'about_body' => ['id' => '[ISI: Tentang]'],
        ]);

        $response = $this->get('/');
        $response->assertDontSee('[ISI:');
        $response = $this->get('/about');
        $response->assertDontSee('[ISI:');
    }

    public function test_recent_projects_are_ordered_by_published_at_desc()
    {
        $project1 = Project::factory()->create([
            'status' => ProjectStatus::Published,
            'is_featured' => false,
            'title' => ['id' => 'Proyek Lama'],
            'published_at' => now()->subDays(5),
            'sort_order' => 10,
        ]);

        $project2 = Project::factory()->create([
            'status' => ProjectStatus::Published,
            'is_featured' => false,
            'title' => ['id' => 'Proyek Baru'],
            'published_at' => now()->subDay(),
            'sort_order' => 1,
        ]);

        $response = $this->get('/');
        $response->assertSeeInOrder(['Proyek Baru', 'Proyek Lama']);
    }

    public function test_project_cards_and_list_items_have_relative_wrapper()
    {
        $project = Project::factory()->create([
            'status' => ProjectStatus::Published,
            'is_featured' => true,
            'title' => ['id' => 'Proyek Tes'],
            'slug' => 'proyek-tes',
        ]);

        // Cek output Blade secara manual atau render response
        $response = $this->get('/');
        $response->assertSee('relative');
        $response->assertSee('absolute inset-0');
    }
}
