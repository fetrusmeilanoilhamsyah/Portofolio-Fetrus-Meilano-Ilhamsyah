<?php

namespace Tests\Feature;

use App\Enums\LinkGroup;
use App\Enums\ProjectStatus;
use App\Models\Link;
use App\Models\LinkHighlight;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialContactSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_does_not_contain_drafts()
    {
        $publishedProject = Project::factory()->create([
            'status' => ProjectStatus::Published,
            'slug' => 'published-project',
        ]);

        $draftProject = Project::factory()->create([
            'status' => ProjectStatus::Draft,
            'slug' => 'draft-project',
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee($publishedProject->slug)
            ->assertDontSee($draftProject->slug);
    }

    public function test_robots_disallows_admin()
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin')
            ->assertDontSee('Disallow: /en/admin')
            ->assertSee('Allow: /')
            ->assertSee('Sitemap: '.route('sitemap'));
    }

    public function test_og_image_is_absolute_url()
    {
        $project = Project::factory()->create([
            'status' => ProjectStatus::Published,
            'slug' => 'seo-project-test',
            'cover_image' => 'covers/test.webp',
        ]);

        $response = $this->get('/projects/'.$project->slug);

        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertMatchesRegularExpression('/<meta\s+property="og:image"\s+content="https?:\/\/[^"]+covers\/test\.webp"/', $content);
    }

    public function test_saluran_page_hides_draft_highlights()
    {
        $channel = Link::factory()->create([
            'group' => LinkGroup::Saluran,
            'is_published' => true,
            'icon' => 'lucide-link',
        ]);

        $publishedHighlight = LinkHighlight::factory()->create([
            'link_id' => $channel->id,
            'is_published' => true,
            'title' => 'Published Highlight Title',
        ]);

        $draftHighlight = LinkHighlight::factory()->create([
            'link_id' => $channel->id,
            'is_published' => false,
            'title' => 'Draft Highlight Title',
        ]);

        $response = $this->get('/social');

        $response->assertStatus(200)
            ->assertSee('Published Highlight Title')
            ->assertDontSee('Draft Highlight Title');
    }

    public function test_contact_page_shows_copy_button_and_contact_links()
    {
        $contact = Link::factory()->create([
            'group' => LinkGroup::Kontak,
            'is_published' => true,
            'label' => 'WhatsApp Contact',
            'url' => 'https://wa.me/1234567890',
            'icon' => 'lucide-link',
        ]);

        $response = $this->get('/contact');

        $response->assertStatus(200)
            ->assertSee('WhatsApp Contact')
            ->assertSee('https://wa.me/1234567890')
            ->assertSee('navigator.clipboard.writeText', false); // copy logic
    }
}
