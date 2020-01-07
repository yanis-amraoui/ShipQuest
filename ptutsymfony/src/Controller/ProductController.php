<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
class ProductController extends AbstractController
{
    /**
     * @Route("/boutique", name="product_index")
     */
    public function index(ProductRepository $productRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'user' => $user
        ]);
    }
    /**
     * @Route("/boutique1", name="product_index")
     */
    public function wsh(ProductRepository $productRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        return $this->render('product/index1.html.twig', [
            'products' => $productRepository->findAll(),
            'user' => $user
        ]);
    }
}