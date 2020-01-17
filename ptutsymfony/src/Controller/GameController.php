<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Tests\Fixtures\ToString;

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
                $data = "0\t" . $username . "\t" . $user->getCoins() . "\t" . $user->getLevel() . "\t"  ;
                $userproduct = $user->getAchat();
                foreach ($userproduct as $item)
                {
                    $data .= $item->getid();
                }

                if($user->getAccountActive())
                    $accountActive = 1;
                else
                    $accountActive = 0;

                $data .= "\t" . $user->getBackgroundActive() . "\t" . $user->getActiveLoadBackground() . "\t" . $user->getSkinActive() . "\t" . $user->getXp() ;
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
     * @Route("/game/savedata", name="game_savedata")
     */
    public function Savedata(UserPasswordEncoderInterface $encoder, ObjectManager $manager,ProductRepository $repo){

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

                    $user->setCoins($_POST["coins"]);
                    $user->setLevel($_POST["level"]);
                    $user->setSkinActive($_POST["activeskin"]);
                    $user->setBackgroundActive($_POST["activebackground"]);
                    $user->setAccountActive($_POST["activeaccount"]);
                    $userskin= str_split($_POST["skins"]);
                    foreach ($userskin as $var){
                        $produits = $repo->find($var);
                        $user->addAchat($produits);
                    }

                    //foreach ($userproduct as $item)
                    //{
                    //    $data .= $item->getid();
                    //}
                    $data = "MAJ ok" . $_POST["coins"] . $user->getCoins();

                    $manager->persist($user);
                    $manager->flush();
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
