<?php

namespace Tests\Feature;

use App\Enums\MediaKind;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Events\ContentChanged;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Models\ProjectMedia;
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
        // Proyek 1 lengkap dengan cover
        $project1 = Project::factory()->withCover()->create(['status' => ProjectStatus::Draft, 'published_at' => null]);
        // Proyek 2 tanpa cover
        $project2 = Project::factory()->create(['status' => ProjectStatus::Draft, 'published_at' => null]);

        $component = Livewire::test(ListProjects::class)
            ->callTableBulkAction('publish', [$project1, $project2])
            ->assertSuccessful();

        // Proyek 1 harus terbit
        $this->assertEquals(ProjectStatus::Published, $project1->refresh()->status);

        // Proyek 2 harus tetap Draft
        $this->assertEquals(ProjectStatus::Draft, $project2->refresh()->status);

        // Verifikasi notifikasi terkirim
        $component->assertNotified('Beberapa proyek dilewati');

        // Test aksi draft
        Livewire::test(ListProjects::class)
            ->callTableBulkAction('draft', [$project1, $project2])
            ->assertSuccessful();

        $this->assertEquals(ProjectStatus::Draft, $project1->refresh()->status);
        // published_at tidak berubah menjadi null oleh aksi ini (sesuai behavior aslinya)
    }

    public function test_regression_project_media_retained_on_edit_without_changes()
    {
        Storage::fake('public');
        Storage::fake('local');

        $project = Project::factory()->create([
            'cover_image' => 'projects/fake-cover.jpg',
        ]);

        ProjectMedia::factory()->create([
            'project_id' => $project->id,
            'path' => 'projects/fake-media.jpg',
            'kind' => MediaKind::Image,
        ]);

        // Letakkan file di disk public
        Storage::disk('public')->put('projects/fake-cover.jpg', 'content');
        Storage::disk('public')->put('projects/fake-media.jpg', 'content');

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->assertSuccessful()
            ->assertFormSet([
                'cover_image' => 'projects/fake-cover.jpg',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('projects/fake-cover.jpg', $project->refresh()->cover_image);
        $this->assertEquals('projects/fake-media.jpg', $project->media()->first()->path);

        Storage::disk('public')->assertExists('projects/fake-cover.jpg');
        Storage::disk('public')->assertExists('projects/fake-media.jpg');
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

    /**
     * 3c — Draft tanpa cover dan tanpa cover_alt bisa disimpan.
     */
    public function test_draft_without_cover_can_be_saved(): void
    {
        Livewire::test(CreateProject::class)
            ->fillForm([
                'title.id' => 'Proyek Draft',
                'summary.id' => 'Ringkasan draft',
                'body.id' => 'Isi draft',
                'status' => ProjectStatus::Draft->value,
                'type' => ProjectType::Web->value,
                'cover_image' => null,
                'cover_alt.id' => '',
                'media' => [],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', ['status' => ProjectStatus::Draft->value]);
    }

    /**
     * 3c — Validasi: cover_image dan cover_alt.id wajib saat status = Published.
     * Menguji closure required(fn (Get $get) => ...) melalui form CreateProject.
     */
    public function test_cover_is_required_when_publishing(): void
    {
        // Tes 1: Status Terbit tanpa cover dan cover_alt.id menghasilkan galat
        Livewire::test(CreateProject::class)
            ->fillForm([
                'title.id' => 'Proyek Terbit',
                'summary.id' => 'Ringkasan',
                'body.id' => 'Isi',
                'status' => ProjectStatus::Published->value,
                'type' => ProjectType::Web->value,
                'cover_image' => null,
                'cover_alt.id' => '',
                'media' => [],
            ])
            ->call('create')
            ->assertHasFormErrors(['cover_image', 'cover_alt.id']);
    }

    /**
     * 3c — Validasi: cover_image dan cover_alt.id TIDAK wajib saat status = Draft.
     */
    public function test_cover_is_not_required_when_saving_draft(): void
    {
        // Tes 2: Status Draft tanpa cover dan cover_alt.id TIDAK menghasilkan galat
        Livewire::test(CreateProject::class)
            ->fillForm([
                'title.id' => 'Proyek Draft',
                'summary.id' => 'Ringkasan draft',
                'body.id' => 'Isi draft',
                'status' => ProjectStatus::Draft->value,
                'type' => ProjectType::Web->value,
                'cover_image' => null,
                'cover_alt.id' => '',
                'media' => [],
            ])
            ->call('create')
            ->assertHasNoFormErrors(['cover_image', 'cover_alt.id']);
    }

    /**
     * 3d — Halaman ubah Proyek menampilkan alt dan caption item media (id dan en)
     *       dari proyek tersimpan, bukan hanya bertahan setelah disimpan.
     *
     * Cara yang sah di Filament 5: setelah mount (assertSuccessful),
     * periksa state Livewire component langsung via component data.
     * Repeater menyimpan state dalam properti `data` Livewire.
     */
    public function test_edit_project_shows_media_alt_and_caption_from_database(): void
    {
        $project = Project::factory()->create([
            'title' => ['id' => 'Proyek Media', 'en' => 'Media Project'],
            'summary' => ['id' => 'Sum'],
            'body' => ['id' => 'Body'],
        ]);

        $project->media()->create([
            'kind' => 'embed',
            'url' => 'http://example.com/embed',
            'alt' => ['id' => 'Alt Bahasa ID', 'en' => 'Alt English'],
            'caption' => ['id' => 'Keterangan ID', 'en' => 'Caption EN'],
            'sort_order' => 0,
        ]);

        // Mount halaman edit dan pastikan berhasil dirender
        $component = Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->assertSuccessful();

        // Ambil state form dari properti data Livewire
        $formData = $component->get('data');

        // Repeater media harus ada dan berisi satu item
        $this->assertArrayHasKey('media', $formData, 'State form harus memiliki kunci media');
        $mediaItems = $formData['media'];
        $this->assertCount(1, $mediaItems, 'Harus ada tepat satu item media');

        $firstItem = reset($mediaItems);

        // Periksa bahwa alt dan caption sudah terisi dari database (bukan hanya array kosong)
        $this->assertArrayHasKey('alt', $firstItem, 'Item media harus memiliki kunci alt');
        $this->assertEquals('Alt Bahasa ID', $firstItem['alt']['id'] ?? null, 'alt.id harus terisi dari database');
        $this->assertEquals('Alt English', $firstItem['alt']['en'] ?? null, 'alt.en harus terisi dari database');
        $this->assertArrayHasKey('caption', $firstItem, 'Item media harus memiliki kunci caption');
        $this->assertEquals('Keterangan ID', $firstItem['caption']['id'] ?? null, 'caption.id harus terisi dari database');
        $this->assertEquals('Caption EN', $firstItem['caption']['en'] ?? null, 'caption.en harus terisi dari database');
    }
}
