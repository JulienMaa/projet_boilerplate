<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;

class CsvExporter
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function exportToCsv(): Response
    {
        $products = $this->productRepository->findAll();
        $csvContent = "\"Name\";\"Description\";\"Price\"\n";

        foreach ($products as $product) {
            $name = $product->getName();
            $description = $product->getDescription();
            $price = $product->getPrice();

            $csvContent .= sprintf("\"%s\";\"%s\";\"%.2f\"\n", $name, $description, $price);
        }

        $response = new Response($csvContent);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="products.csv"');
        $response->headers->set('Cache-Control', 'no-store');

        return $response;
    }
}
