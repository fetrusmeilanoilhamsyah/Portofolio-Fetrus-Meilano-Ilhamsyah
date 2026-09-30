<?php

namespace Tests\Feature\Pages;

use App\Enums\ExperienceKind;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Certificate;
use App\Models\Experience;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceProjectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SiteSetting::factory()->create();
    }

    public function test_experience_page_shows_published_items_only()
    {
        $publishedExp = Experience::factory()->create([
            'is_published' => true,
            'title' => ['id' => 'Pengalaman Terbit'],
            'kind' => ExperienceKind::Kerja,
        ]);

        $draftExp = Experience::factory()->create([
            'is_published' => false,
            'title' => ['id' => 'Pengalaman Draft'],
            'kind' => ExperienceKind::Magang,
        ]);

        $publishedCert = Certificate::factory()->create([
            'is_published' => true,
            'title' => 'Sertifikat Terbit',
        ]);

        $draftCert = Certificate::factory()->create([
            'is_published' => false,
            'title' => 'Sertifikat Draft',
        ]);

        $response = $this->get(route('experience'));

        $response->assertStatus(200);
        $response->assertSee('Pengalaman Terbit');
        $response->assertDontSee('Pengalaman Draft');
        $response->assertSee('Sertifikat Terbit');
        $response->assertDontSee('Sertifikat Draft');
    }

    public function test_projects_page_shows_published_projects_and_types()
    {
        $publishedProject = Project::factory()->published()->create([
            'title' => ['id' => 'Proyek Terbit'],
            'type' => ProjectType::Bot,
        ]);

        $draftProject = Project::factory()->create([
            'status' => ProjectStatus::Draft->value,
            'title' => ['id' => 'Proyek Draft'],
            'type' => ProjectType::Web,
        ]);

        $response = $this->get(route('projects'));

        $response->assertStatus(200);
        $response->assertSee('Proyek Terbit');
        $response->assertDontSee('Proyek Draft');

        // Filter type should only show 'Bot' not 'Web'
        $response->assertSee(ProjectType::Bot->label());
        $response->assertDontSee(ProjectType::Web->label());
    }

    public function test_project_show_returns_404_for_draft()
    {
        $draftProject = Project::factory()->create([
            'status' => ProjectStatus::Draft->value,
            'title' => ['id' => 'Proyek Draft'],
        ]);

        $response = $this->get(route('projects.show', $draftProject->slug));

        $response->assertStatus(404);
    }

    public function test_project_show_conditional_links()
    {
        // Project with all links
        $fullProject = Project::factory()->published()->create([
            'telegram_url' => 'https://t.me/bot',
            'site_url' => 'https://example.com',
            'demo_url' => 'https://demo.com',
            'repo_url' => 'https://github.com/test',
        ]);

        $response1 = $this->get(route('projects.show', $fullProject->slug));
        $response1->assertSee('Coba Bot');
        $response1->assertSee('Kunjungi Situs');
        $response1->assertSee('Demo');
        $response1->assertSee('Lihat Kode');

        // Project with only telegram link
        $botProject = Project::factory()->published()->create([
            'telegram_url' => 'https://t.me/bot2',
            'site_url' => null,
            'demo_url' => null,
            'repo_url' => null,
        ]);

        $response2 = $this->get(route('projects.show', $botProject->slug));
        $response2->assertSee('Coba Bot');
        $response2->assertDontSee('Kunjungi Situs');
        $response2->assertDontSee('Demo');
        $response2->assertDontSee('Lihat Kode');
    }
}
