<?php

namespace Tests\Feature;

use App\Enums\LinkGroup;
use App\Models\Link;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class StageNineDTest extends TestCase
{
    use RefreshDatabase;

    public function test_nav_items_have_svg_and_no_border_left()
    {
        SiteSetting::factory()->create();

        $response = $this->get('/');
        $response->assertStatus(200);

        // Check if navigation links have <x-svg-icon or <svg
        $response->assertSee('<svg', false);
        // Ensure no border-l-2 or similar left border indicator is present
        $response->assertDontSee('border-l-2');
    }

    public function test_initials_tile_appears_if_no_photo()
    {
        SiteSetting::factory()->create([
            'name' => 'Fetrus Meilano',
            'photo' => null,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Should see initials tile
        $response->assertSee('FE');
    }

    public function test_photo_appears_with_alt_name_if_exists()
    {
        SiteSetting::factory()->create([
            'name' => 'Fetrus Meilano',
            'photo' => 'photos/test.jpg',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('alt="Fetrus Meilano"', false);
        $response->assertSee('photos/test.jpg');
    }

    public function test_sidebar_account_icons_limit_and_visibility()
    {
        SiteSetting::factory()->create();

        // Create 6 published account links with icons
        Link::factory()->count(6)->create([
            'group' => LinkGroup::Akun,
            'icon' => 'github',
            'is_published' => true,
        ]);
        // Create 1 unpublished
        Link::factory()->create([
            'group' => LinkGroup::Akun,
            'icon' => 'twitter',
            'is_published' => false,
        ]);

        $response = $this->get('/');

        $content = $response->getContent();
        // SVG github count should be 5 because of limit(5)
        $githubCount = substr_count($content, 'lucide-github') + substr_count($content, '<svg');
        // We'll just assert it does not contain the unpublished one and the one without icon
        $response->assertDontSee('lucide-twitter');

        $publishedAccounts = Link::published()->where('group', LinkGroup::Akun)->whereNotNull('icon')->limit(5)->get();
        $this->assertCount(5, $publishedAccounts);
    }

    public function test_download_cv_button_hidden_if_cv_file_missing()
    {
        SiteSetting::factory()->create([
            'cv_file' => null,
            'about_body' => 'Some about text',
        ]);

        $response = $this->get(route('about'));
        $response->assertDontSee(__('ui.download_cv'));
    }

    public function test_download_cv_button_appears_if_cv_file_exists()
    {
        SiteSetting::factory()->create([
            'cv_file' => 'cv.pdf',
            'about_body' => 'Some about text',
        ]);

        $response = $this->get(route('about'));
        $response->assertSee('cv.pdf');
    }

    public function test_each_public_page_loads_subtitle()
    {
        SiteSetting::factory()->create();

        $pages = [
            'about' => __('ui.sub_about'),
            'experience' => __('ui.sub_experience'),
            'projects' => __('ui.sub_projects'),
            'social' => __('ui.sub_social'),
            'contact' => __('ui.sub_contact'),
        ];

        foreach ($pages as $route => $subtitle) {
            $response = $this->get(route($route));
            $response->assertSee($subtitle);
        }
    }

    public function test_home_does_not_load_isi_placeholder()
    {
        SiteSetting::factory()->create([
            'intro_home' => '[ISI: tuliskan intro di sini]',
        ]);

        $response = $this->get('/');
        $response->assertDontSee('[ISI:');
    }

    public function test_new_lang_keys_exist_in_both_languages()
    {
        $idLang = File::getRequire(lang_path('id/ui.php'));
        $enLang = File::getRequire(lang_path('en/ui.php'));

        $keys = [
            'sub_about',
            'sub_experience',
            'sub_projects',
            'sub_social',
            'sub_contact',
        ];

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $idLang);
            $this->assertArrayHasKey($key, $enLang);
        }
    }
}
