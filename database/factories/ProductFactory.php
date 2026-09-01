<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /** Product types sold in the shop. */
    public const TYPES = ['book', 'calendar', 'print'];

    public function definition(): array
    {
        $type = fake()->randomElement(self::TYPES);
        $name = Str::title(fake()->words(2, true)).' '.Str::title($type);

        return [
            'photo_id' => null,
            'name' => $name,
            'description' => fake()->paragraph(3),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 999999),
            'type' => $type,
            'price' => fake()->randomFloat(2, 15, 150),
            'stock' => fake()->numberBetween(0, 50),
            'image' => null,
            'is_available' => true,
        ];
    }

    /** Force a specific product type. */
    public function type(string $type): static
    {
        return $this->state(fn () => ['type' => $type]);
    }
}
