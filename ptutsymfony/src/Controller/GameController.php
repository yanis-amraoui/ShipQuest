<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GameController extends AbstractController
{

    /**
     * @Route("/game/login", name="game_login")
     */
    public function login(UserPasswordEncoderInterface $encoder){

        $username = "";
        $password = "";

        if(isset($_POST["name"]) || isset($_POST["password"])){
            $username = $_POST["name"];
            $password = $_POST["password"];

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['username' => $username]);
            if(!$user){
                $data = "user non valide";
            }
            else{

            $validPassword = $encoder->isPasswordValid($user,$password);

            if ($validPassword) {
                $data = "0\t" . $username . "\t" . $user->getCoins() . "\t" . $user->getLevel() . "\t" ;
                $userproduct = $user->getAchat();
                foreach ($userproduct as $item)
                {
                    $data .= $item->getid();
                }
            }
            else{
                $data = "mot de passe invalide";
                }
            }
        }
        else{
            $data = "pas de variable";
        }


        return $this->render('game/login.html.twig', [
            'data' => $data
        ]);

    }
    /**
     * @Route("/game/registration", name="game_register")
     */
    public function register(UserPasswordEncoderInterface $encoder){

        $username = "";
        $password = "";

        if(isset($_GET["name"]) || isset($_GET["password"])){
            $username = $_GET["name"];
            $password = $_GET["password"];
            $data = $username . $password;

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['username' => $username]);

            $validPassword = $encoder->isPasswordValid($user,$password);


            if ($validPassword) {
                $data = "0\t" . $username;
            }
            else{
                $data = "mot de passe invalide";
            }
        }
        else{
            $data = "pas de variable";
        }


        return $this->render('game/register.html.twig', [
            'data' => $data
        ]);

    }
    /**
     * @Route("/game/articles", name="game_articles")
     */
    public function article(ArticleRepository $repo){

        $article = $repo->findAll();
        
        return $this->render('game/article.html.twig', [
            'articles' => $article
        ]);

    }
    /**
     * @Route("/game/produits", name="game_produits")
     */
    public function produits(ProductRepository $repo){

        $produits = $repo->findAll();

        return $this->render('game/produits.html.twig', [
            'produits' => $produits
        ]);

    }

}
