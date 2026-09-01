<?php

namespace Database\Factories;

use App\Models\Album;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Photo>
 *
 * Image paths are placeholders here; the seeder generates the actual image
 * files and overwrites these paths so the gallery renders real thumbnails.
 */
class PhotoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'album_id' => Album::factory(),
            'title' => Str::title(fake()->words(fake()->numberBetween(1, 3), true)),
            'description' => fake()->sentence(10),
            'image_path' => 'photos/original/placeholder.jpg',
            'medium_path' => 'photos/medium/placeholder.jpg',
            'thumbnail_path' => 'photos/thumbnails/placeholder.jpg',
            'width' => 1600,
            'height' => 1067,
            'file_size' => fake()->numberBetween(150_000, 4_000_000),
            'display_order' => 0,
            'is_featured' => fake()->boolean(20),
        ];
    }
}
