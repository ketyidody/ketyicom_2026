<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Album>
 */
class AlbumFactory extends Factory
{
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(fake()->numberBetween(1, 3), true));

        return [
            'title' => $title,
            'description' => fake()->sentence(12),
            // Explicit unique slug so the model's auto-slug never collides.
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 999999),
            'cover_image' => null,
            'display_order' => 0,
            'is_published' => true,
        ];
    }
}
