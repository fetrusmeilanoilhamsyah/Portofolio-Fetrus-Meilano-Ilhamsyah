<?php

namespace Database\Factories;

use App\Enums\LinkGroup;
use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'group' => fake()->randomElement(LinkGroup::cases())->value,
            'label' => fake()->word(),
            'url' => fake()->url(),
            'icon' => 'link',
            'note' => null,
            'is_published' => false,
            'show_on_cv' => false,
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
