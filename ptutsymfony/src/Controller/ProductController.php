<?php

namespace App\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;
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

        $UserProduct = new ArrayCollection();

        $UserSkin = new ArrayCollection();
        $UserBG = new ArrayCollection();


        foreach ($user->getAchat() as $wshh){
            $UserProduct->add($wshh);
        }

        foreach ($productRepository->findAll() as $data)
        {
            if ($UserProduct->contains($data)) {
        }else{
                if($data->getCategory()->getName() == "Background"){
                    $UserBG->add($data);
                }
                else{
                    $UserSkin->add($data);
                }
            }
        }

        return $this->render('product/index1.html.twig', [
            'products' => $UserSkin,
            'skins' => $UserSkin,
            'backgrounds' => $UserBG,
            'user' => $user
        ]);
    }
}