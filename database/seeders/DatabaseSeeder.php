<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Photo;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const ALBUM_COUNT = 5;

    private const PHOTOS_PER_ALBUM = 10;

    private const PRODUCT_COUNT = 10;

    /**
     * Seed the application's database with an admin account, sample albums,
     * photos (with generated images), and shop products.
     */
    public function run(): void
    {
        $this->seedUsers();
        $albums = $this->seedAlbumsWithPhotos();
        $this->seedProducts($albums->flatMap->photos);
    }

    /**
     * Create the admin account (plus keep the standard test user).
     */
    private function seedUsers(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ketyi.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ],
        );

        $this->command?->info('Admin account: admin@ketyi.com / password');
    }

    /**
     * Create albums, each populated with photos and generated images.
     *
     * @return \Illuminate\Support\Collection<int, Album>
     */
    private function seedAlbumsWithPhotos(): \Illuminate\Support\Collection
    {
        return Album::factory(self::ALBUM_COUNT)
            ->create()
            ->each(function (Album $album, int $albumIndex) {
                // Album cover
                $album->update([
                    'cover_image' => $this->generateImage(
                        'albums', 1600, 900, $albumIndex,
                    ),
                    'display_order' => $albumIndex,
                ]);

                $photos = Photo::factory(self::PHOTOS_PER_ALBUM)
                    ->for($album)
                    ->create();

                $photos->each(function (Photo $photo, int $photoIndex) use ($albumIndex) {
                    $seed = ($albumIndex * self::PHOTOS_PER_ALBUM) + $photoIndex;
                    $filename = Str::uuid().'.jpg';

                    $photo->update([
                        'image_path' => $this->generateImage(
                            'photos/original', 1600, 1067, $seed, $filename,
                        ),
                        'medium_path' => $this->generateImage(
                            'photos/medium', 1200, 800, $seed, $filename,
                        ),
                        'thumbnail_path' => $this->generateImage(
                            'photos/thumbnails', 400, 267, $seed, $filename,
                        ),
                        'display_order' => $photoIndex,
                    ]);
                });
            });
    }

    /**
     * Create shop products across the book / calendar / print types,
     * linking each to a random seeded photo.
     *
     * @param  \Illuminate\Support\Collection<int, Photo>  $photos
     */
    private function seedProducts(\Illuminate\Support\Collection $photos): void
    {
        $types = \Database\Factories\ProductFactory::TYPES; // book, calendar, print

        for ($i = 0; $i < self::PRODUCT_COUNT; $i++) {
            // Cycle through the types so all three are guaranteed to appear.
            $type = $types[$i % count($types)];
            $photo = $photos->isNotEmpty() ? $photos->random() : null;

            $product = Product::factory()->type($type)->create([
                'photo_id' => $photo?->id,
            ]);

            $product->update([
                'image' => $this->generateImage(
                    'products', 1000, 1000, 100 + $i,
                ),
            ]);
        }
    }

    /**
     * Generate a colourful placeholder JPEG on the public disk and return its
     * relative path (matching how the admin controllers store uploads).
     */
    private function generateImage(
        string $directory,
        int $width,
        int $height,
        int $seed,
        ?string $filename = null,
    ): string {
        $filename ??= Str::uuid().'.jpg';
        $path = trim($directory, '/').'/'.$filename;

        // Deterministic two-tone palette derived from the seed.
        [$bg, $accent] = $this->palette($seed);

        $image = Image::create($width, $height)->fill($bg);

        // Diagonal accent band for a bit of visual variety.
        $image->drawRectangle(0, (int) ($height * 0.62), function ($rect) use ($width, $height, $accent) {
            $rect->size($width, (int) ($height * 0.38));
            $rect->background($accent);
        });

        Storage::disk('public')->put($path, (string) $image->toJpeg(75));

        return $path;
    }

    /**
     * Build a [background, accent] hex pair from a seed using an even hue spread.
     *
     * @return array{0: string, 1: string}
     */
    private function palette(int $seed): array
    {
        $hue = ($seed * 47) % 360;

        return [
            $this->hslToHex($hue, 55, 45),
            $this->hslToHex(($hue + 30) % 360, 60, 60),
        ];
    }

    private function hslToHex(float $h, float $s, float $l): string
    {
        $s /= 100;
        $l /= 100;
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;

        [$r, $g, $b] = match (true) {
            $h < 60 => [$c, $x, 0],
            $h < 120 => [$x, $c, 0],
            $h < 180 => [0, $c, $x],
            $h < 240 => [0, $x, $c],
            $h < 300 => [$x, 0, $c],
            default => [$c, 0, $x],
        };

        return sprintf(
            '#%02x%02x%02x',
            (int) round(($r + $m) * 255),
            (int) round(($g + $m) * 255),
            (int) round(($b + $m) * 255),
        );
    }
}
