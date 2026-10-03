<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingsCvTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_settings_can_save_cv_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        SiteSetting::factory()->create([
            'cv_summary' => null,
            'cv_show_photo' => false,
        ]);

        Livewire::test(SiteSettings::class)
            ->fillForm([
                'cv_summary.id' => 'Ringkasan singkat ID',
                'cv_summary.en' => 'Short summary EN',
                'cv_show_photo' => true,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $setting = SiteSetting::current();

        $this->assertTrue($setting->cv_show_photo);
        $this->assertEquals('Ringkasan singkat ID', $setting->getTranslation('cv_summary', 'id'));
        $this->assertEquals('Short summary EN', $setting->getTranslation('cv_summary', 'en'));
    }
}
