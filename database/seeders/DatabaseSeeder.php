<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
            ]
        );

        $products = [
            ['DAYDREAM', 'women', 'sweet, airy, cloudlike', 'morning light and soft clouds'],
            ['LIORA', 'women', 'floral, playful, delicate', 'a garden in full bloom'],
            ['SOLENNE', 'women', 'elegant, soft, musky', 'quiet evenings and silk'],
            ['LILAC CREST', 'women', 'floral, powdery, mysterious', 'violet petals and twilight'],
            ['AFTERLIGHT', 'women', 'sweet, airy, cloudlike', 'golden skies after sunset'],
            ['VELVET', 'women', 'warm, spicy, seductive', 'soft velvet evenings'],
            ['LUMIERE', 'women', 'delicate, crisp, pear-like', 'morning pear and clean linen'],
            ['BLUSHINE', 'women', 'soft, powdery feminine', 'a warm blush of powder'],
            ['DUSK SERENADE', 'women', 'warm, vanilla, romantic', 'vanilla dusk and quiet music'],
            ['SAFFRON HORIZON', 'women', 'golden, warm, radiant', 'saffron light over the horizon'],
            ['MIDNIGHT ARC', 'men', 'smoky, woody, intense', 'midnight woods and amber smoke'],
            ['ZEPHYR', 'men', 'fresh, airy, breezy', 'clean wind over open water'],
            ['SCARLET WHISPER', 'men', 'fruity, vibrant, romantic', 'scarlet fruit and warm spice'],
            ['OCEAN VERSE', 'men', 'fresh, aquatic, clean', 'salt air and clean waves'],
            ['VALEUR', 'men', 'fresh, bold, aromatic', 'bold herbs and crisp citrus'],
            ['COBALT EMBER', 'men', 'spicy, bold, invigorating', 'cool spice over warm woods'],
            ['CRIMSON VEIL', 'unisex', 'warm, amber, spicy', 'amber warmth under crimson silk'],
            ['SLYVAN', 'unisex', 'woody, warm, sensual', 'soft woods and quiet warmth'],
            ['APRICOT MUSE', 'unisex', 'sweet, fruity, floral', 'apricot nectar and fresh petals'],
        ];

        foreach ($products as [$name, $collection, $scent, $inspiration]) {
            Product::query()->updateOrCreate(
                ['name' => $name],
                [
                    'collection' => $collection,
                    'scent' => $scent,
                    'inspiration' => $inspiration,
                    'stock' => 25,
                    'low_stock_threshold' => 5,
                    'is_active' => true,
                ]
            );
        }
    }
}
