<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Project;
use App\Models\ProjectMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectModelTest extends TestCase
{
    use RefreshDatabase;

    /** Nilai bawaan status harus draft, is_featured false */
    public function test_default_values(): void
    {
        $project = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Proyek Pertama'],
            'summary' => ['id' => 'Ringkasan proyek'],
        ]);

        $this->assertEquals(ProjectStatus::Draft, $project->status);
        $this->assertFalse($project->is_featured);
        $this->assertNull($project->published_at);
    }

    /** Scope published hanya mengembalikan proyek berstatus published */
    public function test_scope_published(): void
    {
        Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Draft Project'],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        $published = Project::factory()->published()->create();

        $results = Project::published()->get();

        $this->assertCount(1, $results);
        $this->assertEquals($published->id, $results->first()->id);
    }

    /** published_at terisi otomatis saat pertama kali diterbitkan */
    public function test_published_at_set_once(): void
    {
        $project = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Proyek'],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        $this->assertNull($project->published_at);

        $project->update(['status' => ProjectStatus::Published]);
        $project->refresh();

        $this->assertNotNull($project->published_at);
        $firstPublishedAt = $project->published_at;

        // Ubah ke draft lalu terbitkan lagi — published_at tidak berubah
        $project->update(['status' => ProjectStatus::Draft]);
        $project->update(['status' => ProjectStatus::Published]);
        $project->refresh();

        $this->assertEquals($firstPublishedAt->toDateTimeString(), $project->published_at->toDateTimeString());
    }

    /** Slug dibuat otomatis dari judul Indonesia */
    public function test_slug_auto_generated_from_id_title(): void
    {
        $project = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Proyek Keren Saya'],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        $this->assertEquals('proyek-keren-saya', $project->slug);
    }

    /** Slug unik: tambah akhiran angka jika ada bentrok */
    public function test_slug_unique_with_suffix(): void
    {
        $p1 = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Proyek Sama'],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        $p2 = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Proyek Sama'],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        $p3 = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Proyek Sama'],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        $this->assertEquals('proyek-sama', $p1->slug);
        $this->assertEquals('proyek-sama-1', $p2->slug);
        $this->assertEquals('proyek-sama-2', $p3->slug);
    }

    /** Slug tidak berubah setelah proyek diterbitkan */
    public function test_slug_immutable_after_published(): void
    {
        $project = Project::factory()->published()->create([
            'title' => ['id' => 'Proyek Asli'],
        ]);

        $originalSlug = $project->slug;

        // Coba ubah slug secara eksplisit
        $project->update(['slug' => 'slug-baru']);
        $project->refresh();

        $this->assertEquals($originalSlug, $project->slug);
    }

    /** Slug masih bisa berubah selama masih draft */
    public function test_slug_mutable_while_draft(): void
    {
        $project = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Draft Proyek'],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        $this->assertEquals(ProjectStatus::Draft, $project->status);

        $project->update(['slug' => 'slug-manual']);
        $project->refresh();

        $this->assertEquals('slug-manual', $project->slug);
    }

    /** Hapus proyek harus menghapus media berantai */
    public function test_cascade_delete_project_media(): void
    {
        $project = Project::factory()->create();
        $media = ProjectMedia::factory()->count(3)->create(['project_id' => $project->id]);

        $this->assertCount(3, ProjectMedia::where('project_id', $project->id)->get());

        $project->delete();

        $this->assertCount(0, ProjectMedia::where('project_id', $project->id)->get());
    }
}
