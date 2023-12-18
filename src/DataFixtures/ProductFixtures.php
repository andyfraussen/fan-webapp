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
            ['label' => 'Product 1', 'price' => 10.99, 'description' => 'Description for product 1', 'image' => 'img/products/001.jpg', 'sku' => '001'],
            ['label' => 'Product 2', 'price' => 15.99, 'description' => 'Description for product 2', 'image' => 'img/products/002.jpg', 'sku' => '002'],
        ];
        foreach ($products as $productData) {
            $product = new Product();
            $product->setLabel($productData['label']);
            $product->setPrice($productData['price']);
            $product->setDescription($productData['description']);
            $product->setSku($productData['sku']);
            $product->setImage($productData["image"]);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
