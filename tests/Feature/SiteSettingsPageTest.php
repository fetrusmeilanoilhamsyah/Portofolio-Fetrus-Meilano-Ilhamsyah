<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['email' => config('portfolio.admin_email')]));
    }

    public function test_can_render_site_settings_page()
    {
        Livewire::test(SiteSettings::class)
            ->assertSuccessful();
    }

    public function test_can_save_site_settings()
    {
        Livewire::test(SiteSettings::class)
            ->fillForm([
                'name' => 'Nama Baru',
                'role.id' => 'Peran A',
                'intro_home.id' => 'Intro A',
                'about_body.id' => 'About A',
                'skills' => [
                    ['group' => 'Frontend', 'items' => 'React, Vue'],
                ],
                'open_to_work' => true,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $setting = SiteSetting::current();

        $this->assertEquals('Nama Baru', $setting->name);
        $this->assertEquals('Peran A', $setting->getTranslation('role', 'id'));
        $this->assertTrue($setting->open_to_work);
        $this->assertCount(1, $setting->skills);
        $this->assertEquals('Frontend', $setting->skills[0]['group']);
    }
}
