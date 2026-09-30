<?php

namespace Tests\Feature;

use App\Enums\ExperienceKind;
use App\Filament\Resources\Experiences\Pages\CreateExperience;
use App\Filament\Resources\Experiences\Pages\EditExperience;
use App\Filament\Resources\Experiences\Pages\ListExperiences;
use App\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExperienceResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['email' => config('portfolio.admin_email')]));
    }

    public function test_can_render_list_experiences()
    {
        Experience::factory()->create();

        Livewire::test(ListExperiences::class)
            ->assertSuccessful();
    }

    public function test_can_create_experience()
    {
        Livewire::test(CreateExperience::class)
            ->fillForm([
                'kind' => ExperienceKind::Kerja->value,
                'organization' => 'Company A',
                'title.id' => 'Posisi A',
                'title.en' => 'Position A',
                'started_at' => '2023-01-01',
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('experiences', [
            'organization' => 'Company A',
        ]);

        $experience = Experience::first();
        $this->assertEquals('Posisi A', $experience->getTranslation('title', 'id'));
    }

    public function test_can_edit_experience()
    {
        $experience = Experience::factory()->create([
            'title' => ['id' => 'Posisi Lama', 'en' => 'Old Position'],
        ]);

        Livewire::test(EditExperience::class, ['record' => $experience->getRouteKey()])
            ->assertSuccessful()
            ->assertFormSet([
                'title.id' => 'Posisi Lama',
                'title.en' => 'Old Position',
            ])
            ->fillForm([
                'title.id' => 'Posisi Baru',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('Posisi Baru', $experience->refresh()->getTranslation('title', 'id'));
    }
}
