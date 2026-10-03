<?php

namespace Tests\Feature\Pages;

use App\Models\Experience;
use App\Models\Link;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CvPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_cv_page_returns_200()
    {
        SiteSetting::factory()->create();

        $response = $this->get(route('cv'));
        $response->assertStatus(200);

        $responseEn = $this->get(route('en.cv'));
        $responseEn->assertStatus(200);
    }

    public function test_cv_page_shows_only_published_and_show_on_cv_entries()
    {
        SiteSetting::factory()->create([
            'name' => 'John Doe',
        ]);

        // Experience 1: Published and show on cv
        $exp1 = Experience::factory()->published()->create([
            'title' => ['id' => 'Software Engineer ID', 'en' => 'Software Engineer EN'],
            'show_on_cv' => true,
        ]);

        // Experience 2: Published but NOT show on cv
        $exp2 = Experience::factory()->published()->create([
            'title' => ['id' => 'Hidden Job ID', 'en' => 'Hidden Job EN'],
            'show_on_cv' => false,
        ]);

        // Experience 3: Draft and show on cv
        $exp3 = Experience::factory()->create([
            'title' => ['id' => 'Draft Job ID', 'en' => 'Draft Job EN'],
            'is_published' => false,
            'show_on_cv' => true,
        ]);

        $response = $this->get(route('cv'));
        $response->assertStatus(200);

        // Name is rendered
        $response->assertSeeText('John Doe');

        // Exp 1 is rendered
        $response->assertSeeText('Software Engineer ID');

        // Exp 2 and 3 are NOT rendered
        $response->assertDontSeeText('Hidden Job ID');
        $response->assertDontSeeText('Draft Job ID');
    }

    public function test_cv_page_shows_contacts_properly()
    {
        SiteSetting::factory()->create();

        // Contact link 1: Show on CV
        Link::factory()->published()->create([
            'group' => 'kontak',
            'label' => 'Email Me',
            'url' => 'mailto:test@example.com',
            'show_on_cv' => true,
        ]);

        // Contact link 2: Phone number should be skipped
        Link::factory()->published()->create([
            'group' => 'kontak',
            'label' => 'Call Me',
            'url' => 'tel:123456789',
            'icon' => 'phone',
            'show_on_cv' => true,
        ]);

        $response = $this->get(route('cv'));

        $response->assertSeeText('Email Me');
        $response->assertDontSeeText('Call Me');
        $response->assertDontSeeText('123456789');
    }

    public function test_cv_page_shows_photo_when_conditions_met()
    {
        SiteSetting::factory()->create([
            'photo' => 'site/photo.jpg',
            'cv_show_photo' => true,
        ]);

        $response = $this->get(route('cv'));
        $response->assertSee('cv-photo');
        $response->assertSee('site/photo.jpg');
    }

    public function test_cv_page_hides_photo_when_cv_show_photo_is_false()
    {
        SiteSetting::factory()->create([
            'photo' => 'site/photo2.jpg',
            'cv_show_photo' => false,
        ]);

        $response2 = $this->get(route('cv'));
        $response2->assertDontSee('cv-photo');
        $response2->assertDontSee('site/photo2.jpg');
    }

    public function test_cv_page_does_not_show_isi_markers()
    {
        SiteSetting::factory()->create([
            'cv_summary' => ['id' => '[ISI: summary]', 'en' => '[ISI: summary EN]'],
        ]);

        Experience::factory()->published()->create([
            'title' => ['id' => '[ISI: title]', 'en' => '[ISI: title EN]'],
            'show_on_cv' => true,
        ]);

        $response = $this->get(route('cv'));
        $response->assertDontSee('[ISI:');
    }

    public function test_cv_page_has_noindex_and_print_button()
    {
        SiteSetting::factory()->create();

        $response = $this->get(route('cv'));
        $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $response->assertSee('window.print()', false);
    }
}
