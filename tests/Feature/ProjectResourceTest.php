<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Events\ContentChanged;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('portfolio.admin_email', 'admin@example.com');
        $this->admin = User::factory()->create(['email' => 'admin@example.com']);
        $this->actingAs($this->admin);
    }

    public function test_non_admin_cannot_access_pages()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);
        $this->actingAs($user);

        $this->get(ProjectResource::getUrl('index'))->assertForbidden();
    }

    public function test_list_projects_renders_records()
    {
        $project = Project::factory()->create();

        Livewire::test(ListProjects::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$project]);
    }

    public function test_create_project_and_upload_webp()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg', 100, 100);

        Livewire::test(CreateProject::class)
            ->fillForm([
                'title.id' => 'Proyek Baru',
                'title.en' => 'New Project',
                'summary.id' => 'Ringkasan Baru',
                'body.id' => 'Isi Baru',
                'status' => ProjectStatus::Published->value,
                'type' => ProjectType::Web->value,
                'cover_image' => $file,
                'cover_alt.id' => 'Cover alt',
                'media' => [],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $project = Project::latest()->first();
        $this->assertEquals('Proyek Baru', $project->getTranslation('title', 'id'));
        $this->assertEquals('New Project', $project->getTranslation('title', 'en'));

        $this->assertNotNull($project->published_at);
        $this->assertStringEndsWith('.webp', $project->cover_image);
    }

    public function test_edit_project_hydrates_translations_and_saves()
    {
        $project = Project::factory()->create([
            'title' => ['id' => 'Judul Lama', 'en' => 'Old Title'],
            'summary' => ['id' => 'Sum ID'],
            'body' => ['id' => 'Body ID'],
        ]);

        $project->media()->create([
            'kind' => 'embed',
            'url' => 'http://example.com',
            'alt' => ['id' => 'Alt ID', 'en' => 'Alt EN'],
            'caption' => ['id' => 'Cap ID', 'en' => 'Cap EN'],
        ]);

        // Hydration check
        $component = Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->assertSuccessful()
            ->assertFormSet([
                'title.id' => 'Judul Lama',
                'title.en' => 'Old Title',
                'summary.id' => 'Sum ID',
                'body.id' => 'Body ID',
            ]);

        // Save without changing
        $component->call('save')
            ->assertHasNoFormErrors();

        $project->refresh();
        $this->assertEquals('Old Title', $project->getTranslation('title', 'en'));

        // Ensure media translations persist
        $media = $project->media->first();
        $this->assertEquals('Alt EN', $media->getTranslation('alt', 'en'));
        $this->assertEquals('Cap EN', $media->getTranslation('caption', 'en'));
    }

    public function test_bulk_actions_publish_and_draft()
    {
        $project1 = Project::factory()->create(['status' => ProjectStatus::Draft, 'published_at' => null]);
        $project2 = Project::factory()->create(['status' => ProjectStatus::Draft, 'published_at' => null]);

        Livewire::test(ListProjects::class)
            ->callTableBulkAction('publish', [$project1, $project2])
            ->assertSuccessful();

        $this->assertEquals(ProjectStatus::Published, $project1->refresh()->status);
        $this->assertNotNull($project1->published_at);

        Livewire::test(ListProjects::class)
            ->callTableBulkAction('draft', [$project1, $project2])
            ->assertSuccessful();

        $this->assertEquals(ProjectStatus::Draft, $project1->refresh()->status);
        // published_at remains not null, which is expected
    }

    public function test_media_change_dispatches_content_changed()
    {
        Event::fake([ContentChanged::class]);
        $project = Project::factory()->create();

        // Assert event was fired on creation
        Event::assertDispatched(ContentChanged::class);

        Event::fake([ContentChanged::class]);
        // Update only media
        $project->media()->create(['kind' => 'embed', 'url' => 'http://test.com']);

        Event::assertDispatched(ContentChanged::class);
    }
}
