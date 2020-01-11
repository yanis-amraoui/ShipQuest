<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="cart_index")
     */
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        $panier = $session->get('panier',[]);

        $panierWithData = [];

        foreach ($panier as $id => $quantity){
            $panierWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;

        foreach ($panierWithData as $item){
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total +=$totalItem;
        }
        $session->set('total', $total);
        return $this->render('cart/index.html.twig', [
            'items' => $panierWithData,
            'total' => $total
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add ($id, SessionInterface $session){


        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);

       return $this->redirectToRoute("cart_index");

    }

    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     */
    public function remove($id, SessionInterface $session){

        $panier = $session->get('panier',[]);

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute("cart_index");
    }
    /**
     * @Route("/panier/valider", name="cart_checkout")
     */
    public function checkOut(SessionInterface $session,Request $request, ObjectManager $manager, ProductRepository $repo ){
        $user = $this->getUser();

        $userCoins = $user->getCoins();

        $total = $session->get('total');
        $panier = $session->get('panier',[]);
        if($userCoins < $total){
            $message = "Pas assez de sous, je t'offre 2000 fdp";
            $user->setCoins(2000);
            $manager->persist($user);
            $manager->flush();
        }else{
            foreach ($panier as $NumProd => $qte)
            {
                $produits = $repo->find($NumProd);
                $user->addAchat($produits);
            }
            $user->setCoins($userCoins-$total);
            $manager->persist($user);
            $manager->flush();
            $message = "le paiement c'est bien passer ";
        }



        dd($userCoins, $total,$message,$panier);
    }
    /**
     * @Route("/coins", name="cart_coins")
     */
    public function addCoins(){
        return $this->render('cart/coins.html.twig');
    }
}
