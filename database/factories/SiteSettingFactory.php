<?php

namespace Database\Factories;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteSetting>
 */
class SiteSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'role' => ['id' => 'Developer', 'en' => 'Developer'],
            'intro_home' => ['id' => fake()->sentence(10), 'en' => fake()->sentence(10)],
            'about_body' => ['id' => fake()->paragraph(3), 'en' => fake()->paragraph(3)],
            'photo' => null,
            'cv_file' => null,
            'location' => fake()->city(),
            'open_to_work' => false,
            'open_to_work_note' => null,
            'skills' => null,
            'og_image' => null,
        ];
    }
}
