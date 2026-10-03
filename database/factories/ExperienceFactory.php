<?php

namespace Database\Factories;

use App\Enums\ExperienceKind;
use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kind' => fake()->randomElement(ExperienceKind::cases())->value,
            'title' => ['id' => fake()->jobTitle(), 'en' => fake()->jobTitle()],
            'organization' => fake()->company(),
            'location' => fake()->city(),
            'description' => ['id' => fake()->paragraph(2), 'en' => fake()->paragraph(2)],
            'logo' => null,
            'started_at' => fake()->date(),
            'ended_at' => null,
            'is_published' => false,
            'show_on_cv' => true,
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
