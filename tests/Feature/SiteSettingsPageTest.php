<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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

    public function test_regression_site_settings_files_retained_on_edit_without_changes()
    {
        Storage::fake('public');

        $setting = SiteSetting::current();
        $setting->update([
            'name' => 'Nama Default',
            'role' => ['id' => 'Peran Default', 'en' => 'Default Role'],
            'intro_home' => ['id' => 'Intro Default', 'en' => 'Default Intro'],
            'about_body' => ['id' => 'About Default', 'en' => 'Default About'],
            'photo' => 'settings/fake-photo.jpg',
            'cv_file' => 'settings/fake-cv.pdf',
            'og_image' => 'settings/fake-og.jpg',
        ]);

        Storage::disk('public')->put('settings/fake-photo.jpg', 'content');
        Storage::disk('public')->put('settings/fake-cv.pdf', 'content');
        Storage::disk('public')->put('settings/fake-og.jpg', 'content');

        Livewire::test(SiteSettings::class)
            ->assertSuccessful()
            ->assertFormSet([
                'photo' => 'settings/fake-photo.jpg',
                'cv_file' => 'settings/fake-cv.pdf',
                'og_image' => 'settings/fake-og.jpg',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $setting->refresh();
        $this->assertEquals('settings/fake-photo.jpg', $setting->photo);
        $this->assertEquals('settings/fake-cv.pdf', $setting->cv_file);
        $this->assertEquals('settings/fake-og.jpg', $setting->og_image);

        Storage::disk('public')->assertExists('settings/fake-photo.jpg');
        Storage::disk('public')->assertExists('settings/fake-cv.pdf');
        Storage::disk('public')->assertExists('settings/fake-og.jpg');
    }
}
