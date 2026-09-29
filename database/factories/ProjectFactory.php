<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $titleId = fake()->sentence(3);

        return [
            'slug' => Str::slug($titleId).'-'.fake()->unique()->numberBetween(1, 9999),
            'type' => fake()->randomElement(ProjectType::cases())->value,
            'title' => ['id' => $titleId, 'en' => fake()->sentence(3)],
            'summary' => ['id' => fake()->sentence(10), 'en' => fake()->sentence(10)],
            'body' => ['id' => fake()->paragraph(3), 'en' => fake()->paragraph(3)],
            'stack' => fake()->randomElements(['PHP', 'Laravel', 'Vue', 'React', 'Python', 'Node.js'], 3),
            'cover_image' => null,
            'cover_alt' => ['id' => fake()->sentence(3), 'en' => fake()->sentence(3)],
            'telegram_url' => null,
            'site_url' => null,
            'demo_url' => null,
            'repo_url' => null,
            'started_at' => fake()->date(),
            'ended_at' => null,
            'is_featured' => false,
            'status' => ProjectStatus::Draft->value,
            'published_at' => null,
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProjectStatus::Published->value,
            'published_at' => now(),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
