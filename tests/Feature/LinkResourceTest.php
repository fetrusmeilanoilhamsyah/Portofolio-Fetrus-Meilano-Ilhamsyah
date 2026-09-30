<?php

namespace Tests\Feature;

use App\Enums\LinkGroup;
use App\Filament\Resources\Links\Pages\CreateLink;
use App\Filament\Resources\Links\Pages\EditLink;
use App\Filament\Resources\Links\Pages\ListLinks;
use App\Filament\Resources\Links\RelationManagers\LinkHighlightsRelationManager;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LinkResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['email' => config('portfolio.admin_email')]));
    }

    public function test_can_render_list_links()
    {
        Link::factory()->create();

        Livewire::test(ListLinks::class)
            ->assertSuccessful();
    }

    public function test_can_create_link()
    {
        Livewire::test(CreateLink::class)
            ->fillForm([
                'group' => LinkGroup::Saluran->value,
                'label' => 'Saluran A',
                'url' => 'https://example.com',
                'icon' => 'youtube',
                'note.id' => 'Catatan A',
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('links', [
            'label' => 'Saluran A',
            'icon' => 'youtube',
        ]);

        $link = Link::first();
        $this->assertEquals('Catatan A', $link->getTranslation('note', 'id'));
    }

    public function test_can_edit_link()
    {
        $link = Link::factory()->create([
            'note' => ['id' => 'Catatan Lama', 'en' => 'Old Note'],
        ]);

        Livewire::test(EditLink::class, ['record' => $link->getRouteKey()])
            ->assertSuccessful()
            ->assertFormSet([
                'note.id' => 'Catatan Lama',
                'note.en' => 'Old Note',
            ])
            ->fillForm([
                'note.id' => 'Catatan Baru',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('Catatan Baru', $link->refresh()->getTranslation('note', 'id'));
    }

    public function test_can_add_highlight_only_if_channel()
    {
        $channelLink = Link::factory()->create(['group' => LinkGroup::Saluran]);
        $accountLink = Link::factory()->create(['group' => LinkGroup::Akun]);

        // Mock Livewire component for RelationManager
        // For channel, canCreate should be true. We can test it through the UI by mounting it.
        $channelManager = Livewire::test(LinkHighlightsRelationManager::class, [
            'ownerRecord' => $channelLink,
            'pageClass' => EditLink::class,
        ]);

        $channelManager->assertTableActionVisible('create');

        $accountManager = Livewire::test(LinkHighlightsRelationManager::class, [
            'ownerRecord' => $accountLink,
            'pageClass' => EditLink::class,
        ]);

        $accountManager->assertTableActionHidden('create');
    }
}
