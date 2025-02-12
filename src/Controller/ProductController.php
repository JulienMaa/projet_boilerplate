<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Security\Voter\ProductVoter;
use App\Service\CsvExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/product')]
class ProductController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/list', name: 'app_product_list')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], ['price' => 'DESC']);

        return $this->render('product/list.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'app_product_add')]
    public function add(Request $request, AuthorizationCheckerInterface $authChecker): Response
    {
        $product = new Product();

        if (!$this->isGranted(ProductVoter::CREATE, $product)) {
            $this->addFlash('error', 'You do not have permission to create a product.');
            return $this->redirectToRoute('app_product_list');
        }
        
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Product added successfully!');
            return $this->redirectToRoute('app_product_list');
        }

        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_product_edit')]
    public function edit(Product $product, Request $request): Response
    {
        if (!$this->isGranted(ProductVoter::EDIT, $product)) {
            $this->addFlash('error', 'You do not have permission to edit this product.');
            return $this->redirectToRoute('app_product_list');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Product updated successfully!');
            return $this->redirectToRoute('app_product_list');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_product_delete')]
    public function delete(Product $product): Response
    {
        if (!$this->isGranted(ProductVoter::DELETE, $product)) {
            $this->addFlash('error', 'You do not have permission to delete this product.');
            return $this->redirectToRoute('app_product_list');
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->addFlash('success', 'Product deleted successfully!');
        return $this->redirectToRoute('app_product_list');
    }

    #[Route('/export-csv', name: 'app_product_export_csv')]
    public function exportCsv(CsvExporter $csvExporter): Response
    {
        return $csvExporter->exportToCsv();
    }
}
