<?php

namespace Database\Factories;

use App\Enums\MediaKind;
use App\Models\Project;
use App\Models\ProjectMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectMedia>
 */
class ProjectMediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'kind' => fake()->randomElement(MediaKind::cases())->value,
            'path' => 'media/'.fake()->uuid().'.jpg',
            'url' => null,
            'caption' => ['id' => fake()->sentence(5), 'en' => fake()->sentence(5)],
            'alt' => ['id' => fake()->sentence(3), 'en' => fake()->sentence(3)],
            'poster' => null,
            'sort_order' => 0,
        ];
    }
}
