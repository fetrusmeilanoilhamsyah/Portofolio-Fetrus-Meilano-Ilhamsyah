<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Experience;
use App\Models\Link;
use App\Models\LinkHighlight;
use App\Models\Project;
use App\Models\ProjectMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_experience_scopes()
    {
        $published = Experience::factory()->create(['is_published' => true, 'sort_order' => 2]);
        $draft = Experience::factory()->create(['is_published' => false, 'sort_order' => 1]);

        $publishedExperiences = Experience::published()->get();
        $this->assertCount(1, $publishedExperiences);
        $this->assertTrue($publishedExperiences->first()->is($published));

        $orderedExperiences = Experience::ordered()->get();
        $this->assertTrue($orderedExperiences->first()->is($draft));
        $this->assertTrue($orderedExperiences->last()->is($published));
    }

    public function test_certificate_scopes()
    {
        $published = Certificate::factory()->create(['is_published' => true, 'sort_order' => 2]);
        $draft = Certificate::factory()->create(['is_published' => false, 'sort_order' => 1]);

        $publishedCertificates = Certificate::published()->get();
        $this->assertCount(1, $publishedCertificates);
        $this->assertTrue($publishedCertificates->first()->is($published));

        $orderedCertificates = Certificate::ordered()->get();
        $this->assertTrue($orderedCertificates->first()->is($draft));
        $this->assertTrue($orderedCertificates->last()->is($published));
    }

    public function test_link_highlight_scopes()
    {
        $link = Link::factory()->create();
        $published = LinkHighlight::factory()->create(['link_id' => $link->id, 'is_published' => true, 'sort_order' => 2]);
        $draft = LinkHighlight::factory()->create(['link_id' => $link->id, 'is_published' => false, 'sort_order' => 1]);

        $publishedHighlights = LinkHighlight::published()->get();
        $this->assertCount(1, $publishedHighlights);
        $this->assertTrue($publishedHighlights->first()->is($published));

        $orderedHighlights = LinkHighlight::ordered()->get();
        $this->assertTrue($orderedHighlights->first()->is($draft));
        $this->assertTrue($orderedHighlights->last()->is($published));
    }

    public function test_project_media_ordered_scope()
    {
        $project = Project::factory()->create();
        $media2 = ProjectMedia::factory()->create(['project_id' => $project->id, 'sort_order' => 2]);
        $media1 = ProjectMedia::factory()->create(['project_id' => $project->id, 'sort_order' => 1]);

        $orderedMedia = ProjectMedia::ordered()->get();
        $this->assertTrue($orderedMedia->first()->is($media1));
        $this->assertTrue($orderedMedia->last()->is($media2));
    }
}
