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
     * @Route("/cart", name="cart_index")
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
     * @Route("/cart/add/{id}", name="cart_add")
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
     * @Route("/cart/remove/{id}", name="cart_remove")
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
     * @Route("/cart/checkout", name="cart_checkout")
     */
    public function checkOut(SessionInterface $session,Request $request, ObjectManager $manager, ProductRepository $repo ){
        $user = $this->getUser();

        $userCoins = $user->getCoins();

        $total = $session->get('total');
        $panier = $session->get('panier',[]);
        if(empty($panier)){
            $message = "Vous n'avez aucun article dans le panier ! Veuillez retourner à la boutique.";
        }
        else if($userCoins < $total){
            $message = "Tu n'as pas assez de crédits ! Passe faire un tour à la boutique !";
        }else{
            foreach ($panier as $NumProd => $qte)
            {
                $produits = $repo->find($NumProd);
                $user->addAchat($produits);
            }
            $user->setCoins($userCoins-$total);
            $manager->persist($user);
            $manager->flush();
            $message = "Le paiement s'est bien déroulé, il vous reste : "  . $user->getCoins() . " crédits.";
        }

        return $this->render('cart/checkout.html.twig', [
        'data' => $message]
           );
    }
    /**
     * @Route("/coins", name="cart_coins")
     */
    public function addCoins(){
        return $this->render('cart/coins.html.twig');
    }
}
