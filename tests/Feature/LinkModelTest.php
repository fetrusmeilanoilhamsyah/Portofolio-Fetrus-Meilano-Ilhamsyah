<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\LinkHighlight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkModelTest extends TestCase
{
    use RefreshDatabase;

    /** Scope published hanya mengembalikan link yang diterbitkan */
    public function test_scope_published(): void
    {
        Link::factory()->create(['is_published' => false]);
        $published = Link::factory()->published()->create();

        $results = Link::published()->get();

        $this->assertCount(1, $results);
        $this->assertEquals($published->id, $results->first()->id);
    }

    /** Scope ordered mengurutkan berdasarkan sort_order */
    public function test_scope_ordered(): void
    {
        $c = Link::factory()->create(['sort_order' => 3]);
        $a = Link::factory()->create(['sort_order' => 1]);
        $b = Link::factory()->create(['sort_order' => 2]);

        $results = Link::ordered()->get();

        $this->assertEquals([$a->id, $b->id, $c->id], $results->pluck('id')->toArray());
    }

    /** Hapus link harus menghapus highlights berantai */
    public function test_cascade_delete_link_highlights(): void
    {
        $link = Link::factory()->create();
        LinkHighlight::factory()->count(2)->create(['link_id' => $link->id]);

        $this->assertCount(2, LinkHighlight::where('link_id', $link->id)->get());

        $link->delete();

        $this->assertCount(0, LinkHighlight::where('link_id', $link->id)->get());
    }

    /** Nilai bawaan is_published false, sort_order 0 */
    public function test_default_values(): void
    {
        $link = Link::factory()->create();

        $this->assertFalse($link->is_published);
        $this->assertEquals(0, $link->sort_order);
    }
}
