<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectStackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_can_create_project_with_stack_and_clean_duplicates()
    {
        Livewire::test(CreateProject::class)
            ->fillForm([
                'title.id' => 'Test Stack',
                'type' => ProjectType::Web->value,
                'summary.id' => 'Test summary',
                'status' => ProjectStatus::Draft->value,
                'sort_order' => 1,
                'media' => [],
                'stack' => ['Laravel', 'PHP', 'Laravel', '', '  ', 'Tailwind'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $project = Project::first();
        $this->assertNotNull($project);

        // Cek cleaning string kosong dan duplikat
        $this->assertEquals(['Laravel', 'PHP', 'Tailwind'], $project->stack);
    }

    public function test_edit_page_displays_stack()
    {
        $project = Project::factory()->create([
            'stack' => ['Vue', 'Node.js'],
        ]);

        Livewire::test(EditProject::class, ['record' => $project->id])
            ->assertFormSet([
                'stack' => ['Vue', 'Node.js'],
            ]);
    }

    public function test_stack_validation_limits()
    {
        $tooManyTags = array_map(fn ($i) => "Tag $i", range(1, 13));
        $longTag = str_repeat('a', 31);

        Livewire::test(CreateProject::class)
            ->fillForm([
                'title.id' => 'Test',
                'type' => ProjectType::Web->value,
                'summary.id' => 'Test',
                'media' => [],
                'stack' => $tooManyTags,
            ])
            ->call('create')
            ->assertHasFormErrors(['stack']); // max 12 items

        Livewire::test(CreateProject::class)
            ->fillForm([
                'title.id' => 'Test 2',
                'type' => ProjectType::Web->value,
                'summary.id' => 'Test',
                'media' => [],
                'stack' => ['Valid', $longTag],
            ])
            ->call('create')
            ->assertHasFormErrors(['stack.1']); // max 30 characters
    }

    public function test_public_pages_display_escaped_tags_and_handle_empty()
    {
        $project1 = Project::factory()->create([
            'status' => ProjectStatus::Published,
            'stack' => ['<script>alert("XSS")</script>', 'PHP'],
        ]);

        $project2 = Project::factory()->create([
            'status' => ProjectStatus::Published,
            'stack' => [],
        ]);

        // Halaman list /projects
        $response = $this->get('/projects');
        $response->assertOk();

        // Assert HTML di-escape
        $response->assertSee('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert("XSS")</script>', false);

        // Halaman detail
        $responseDetail = $this->get('/projects/'.$project1->slug);
        $responseDetail->assertOk();
        $responseDetail->assertSee('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', false);

        // Halaman detail proyek kosong stack
        $responseEmpty = $this->get('/projects/'.$project2->slug);
        $responseEmpty->assertOk();
    }
}
