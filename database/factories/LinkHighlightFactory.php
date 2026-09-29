<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\LinkHighlight;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LinkHighlight>
 */
class LinkHighlightFactory extends Factory
{
    public function definition(): array
    {
        return [
            'link_id' => Link::factory(),
            'title' => ['id' => fake()->sentence(3), 'en' => fake()->sentence(3)],
            'summary' => ['id' => fake()->sentence(10), 'en' => fake()->sentence(10)],
            'url' => fake()->optional()->url(),
            'highlighted_at' => fake()->date(),
            'is_published' => false,
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }
}
