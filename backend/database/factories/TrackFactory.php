<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Track>
 */
class TrackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'artists' => 'Artista 1, Artista 2',
            'duration' => '03:45',
            'thumb_url' => fake()->imageUrl(300, 300),
            'preview_url' => fake()->url(),
            'spotify_url' => fake()->url(),
            'release_date' => fake()->date(),
            'is_available_in_br' => true,
            'isrc' => $this->faker->unique()->regexify('[A-Z]{2}[A-Z0-9]{3}\d{7}'),
        ];
    }
}
