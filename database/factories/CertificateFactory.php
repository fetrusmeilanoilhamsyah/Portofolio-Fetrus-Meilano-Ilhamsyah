<?php

namespace Database\Factories;

use App\Models\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'issuer' => fake()->company(),
            'category' => fake()->randomElement(['web', 'data', 'cloud', 'security']),
            'issued_at' => fake()->date(),
            'expires_at' => null,
            'credential_url' => null,
            'image' => null,
            'file' => null,
            'alt' => ['id' => fake()->sentence(3), 'en' => fake()->sentence(3)],
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
