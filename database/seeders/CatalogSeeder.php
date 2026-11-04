<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Support\Slug;
use Illuminate\Database\Seeder;

/**
 * Catalogue d'exemple (prix indicatifs) pour présenter la boutique avant l'ajout des vrais produits.
 * Ne fait rien si des produits existent déjà.
 */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::exists()) {
            return;
        }

        $categories = collect([
            ['Baskets', 'basket', 'Sneakers du quotidien et modèles iconiques.'],
            ['Chaussures de ville', 'ville', 'Derbies et richelieus pour le bureau et les cérémonies.'],
            ['Mocassins', 'mocassin', 'L\'élégance sans lacets, du week-end au mariage.'],
            ['Sandales & mules', 'sandale', 'Fraîcheur et confort pour la chaleur d\'Abidjan.'],
            ['Bottines', 'bottine', 'Chelsea, desert boots et montantes.'],
        ])->mapWithKeys(fn ($c, $i) => [$c[1] => Category::create([
            'name' => $c[0], 'slug' => Slug::unique(Category::class, $c[0]), 'silhouette' => $c[1],
            'description' => $c[2], 'position' => $i,
        ])]);

        $brands = collect(['Nike', 'Adidas', 'Puma', 'New Balance', 'Converse', 'Vans', 'Clarks', 'Timberland', 'Lacoste', 'Artisan d\'Abidjan'])
            ->mapWithKeys(fn ($name, $i) => [$name => Brand::create(['name' => $name, 'slug' => Slug::unique(Brand::class, $name), 'position' => $i])]);

        $colors = collect([
            ['Noir', '#141414'], ['Blanc', '#f4f4f2'], ['Marron', '#6b3e26'], ['Camel', '#b07a3b'],
            ['Beige', '#d9c3a0'], ['Bleu marine', '#1f2a44'], ['Gris', '#8a8a8a'], ['Bordeaux', '#6d1a2a'], ['Kaki', '#5b6136'],
        ])->mapWithKeys(fn ($c, $i) => [$c[0] => Color::create(['name' => $c[0], 'slug' => Slug::unique(Color::class, $c[0]), 'hex' => $c[1], 'position' => $i])]);

        // [catégorie, marque, nom, couleur, prix, ancien prix, mis en avant, jours depuis l'ajout, description]
        $products = [
            ['basket', 'Nike', 'Air Force 1 \'07', 'Blanc', 65000, null, true, 40, 'La basket blanche qui va avec tout. Cuir pleine fleur, semelle Air pour l\'amorti, coupe basse.'],
            ['basket', 'Nike', 'Air Force 1 \'07', 'Noir', 65000, null, false, 40, 'La version tout noir de l\'icône : cuir pleine fleur, semelle Air, coupe basse.'],
            ['basket', 'Nike', 'Air Max 90', 'Gris', 85000, 95000, true, 12, 'Bulle d\'air visible au talon, mesh respirant et empiècements en daim. Confort toute la journée.'],
            ['basket', 'Adidas', 'Samba OG', 'Noir', 70000, null, true, 5, 'Le classique terrasse : cuir souple, empiècement en daim, semelle gomme.'],
            ['basket', 'Adidas', 'Stan Smith', 'Blanc', 55000, 62000, false, 60, 'Minimaliste et intemporelle. Tige en cuir, talon contrasté, semelle cousue.'],
            ['basket', 'Puma', 'Suede Classic', 'Bleu marine', 45000, null, false, 30, 'Daim doux, bande Formstrip, semelle caoutchouc : un style rétro qui ne vieillit pas.'],
            ['basket', 'New Balance', '574', 'Gris', 75000, null, true, 8, 'Daim et mesh, semelle ENCAP pour le maintien. La référence confort de New Balance.'],
            ['basket', 'Converse', 'Chuck Taylor All Star', 'Noir', 35000, null, false, 90, 'Toile robuste, semelle vulcanisée, la montante culte depuis 1917.'],
            ['basket', 'Vans', 'Old Skool', 'Noir', 40000, 45000, false, 50, 'Toile et daim, bande latérale signature, semelle gaufrée pour l\'adhérence.'],
            ['basket', 'Lacoste', 'Carnaby Pro', 'Blanc', 60000, null, false, 3, 'Cuir lisse, crocodile brodé, semelle légère : le chic sport.'],
            ['ville', 'Artisan d\'Abidjan', 'Derby cuir lisse', 'Noir', 38000, null, true, 20, 'Cuir de vachette lisse, semelle cuir et gomme, cousu main à Abidjan. Idéal bureau et cérémonies.'],
            ['ville', 'Artisan d\'Abidjan', 'Richelieu bout fleuri', 'Marron', 42000, null, false, 25, 'Perforations fleuries, cuir patiné à la main. Se porte avec un costume ou un pantalon chino.'],
            ['ville', 'Clarks', 'Un Hugh Lace', 'Noir', 72000, 80000, false, 45, 'Derby léger à semelle amortissante : l\'élégance sans les douleurs de fin de journée.'],
            ['mocassin', 'Artisan d\'Abidjan', 'Mocassin à pampilles', 'Bordeaux', 35000, null, true, 2, 'Cuir souple, pampilles, semelle fine. Le mocassin des grandes occasions.'],
            ['mocassin', 'Artisan d\'Abidjan', 'Mocassin daim', 'Camel', 32000, 36000, false, 15, 'Daim velours, semelle gomme flexible. Parfait sans chaussettes le week-end.'],
            ['mocassin', 'Lacoste', 'Mocassin Piloter', 'Bleu marine', 58000, null, false, 70, 'Cuir nubuck, picots de conduite, crocodile en métal.'],
            ['sandale', 'Artisan d\'Abidjan', 'Sandale cuir tressé', 'Marron', 22000, null, false, 10, 'Brides en cuir tressé à la main, semelle anatomique. Fraîche et solide.'],
            ['sandale', 'Nike', 'Claquette Victori One', 'Noir', 18000, null, false, 35, 'Mousse douce et bride rembourrée : l\'indispensable pour la maison et la plage.'],
            ['sandale', 'Adidas', 'Adilette Comfort', 'Bleu marine', 20000, 24000, false, 55, 'Bride emblématique et semelle Cloudfoam extra-douce.'],
            ['bottine', 'Clarks', 'Desert Boot', 'Beige', 78000, null, true, 6, 'Le modèle original : daim, semelle crêpe, deux œillets. Iconique depuis 1950.'],
            ['bottine', 'Timberland', '6-Inch Premium', 'Camel', 110000, 125000, false, 28, 'Nubuck imperméable, col rembourré, semelle crantée : la montante robuste par excellence.'],
            ['bottine', 'Artisan d\'Abidjan', 'Chelsea cuir', 'Noir', 45000, null, false, 1, 'Élastiques latéraux, cuir lisse, talon légèrement surélevé. S\'enfile en une seconde.'],
            ['bottine', 'Timberland', 'Chukka Cityroam', 'Kaki', 85000, null, false, 65, 'Nubuck, semelle légère et amortie : l\'esprit Timberland pour la ville.'],
        ];

        foreach ($products as $i => [$cat, $brand, $name, $color, $price, $old, $featured, $days, $description]) {
            $product = Product::create([
                'category_id' => $categories[$cat]->id,
                'brand_id' => $brands[$brand]->id,
                'color_id' => $colors[$color]->id,
                'name' => $name,
                'slug' => Slug::unique(Product::class, "$brand $name $color"),
                'description' => $description,
                'price' => $price,
                'compare_at_price' => $old,
                'is_featured' => $featured,
                'views_count' => ($i * 37) % 120,
            ]);
            $product->forceFill(['created_at' => now()->subDays($days), 'updated_at' => now()->subDays($days)])->save();

            // Pointures 39 à 46, quelques-unes épuisées : de quoi tester les filtres.
            foreach (range(39, 46) as $size) {
                $product->sizes()->create(['size' => $size, 'stock' => ($i + $size) % 5 === 0 ? 0 : (($i * $size) % 4) + 2]);
            }
        }
    }
}
