<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'name' => 'Evian',
                'description' => 'Sourced from the French Alps, Evian is renowned for its purity and unique mineral composition. A soft, balanced water perfect for daily hydration.',
                'price' => 1.99
            ],
            [
                'name' => 'Cristalline',
                'description' => 'Light and affordable, Cristalline is the ideal spring water for the whole family. Available throughout France, it ensures healthy hydration at all times.',
                'price' => 0.99
            ],
            [
                'name' => 'Matouba',
                'description' => 'Sourced from the Guadeloupe region, Matouba offers exceptional purity. Rich in minerals, it’s a symbol of natural freshness and authenticity.',
                'price' => 2.49
            ],
            [
                'name' => 'Vittel',
                'description' => 'From the Vosges Mountains, Vittel is known for its revitalizing properties. Rich in minerals, it’s the perfect choice to quench your thirst while maintaining a balanced diet.',
                'price' => 1.79
            ],
            [
                'name' => 'Badoit',
                'description' => 'A naturally sparkling water from the Saint-Galmier region, Badoit is famous for its effervescence and refreshing taste. Ideal for pairing with meals or enjoyed on its own.',
                'price' => 2.99
            ],
            [
                'name' => 'Hépar',
                'description' => 'Known for its digestive benefits, Hépar is a mineral water from the Vosges region. Naturally rich in magnesium, it’s perfect for promoting hydration and digestion.',
                'price' => 1.89
            ]
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData['name'])
                    ->setDescription($productData['description'])
                    ->setPrice($productData['price']);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
