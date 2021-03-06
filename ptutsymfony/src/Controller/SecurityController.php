<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="security_registration")
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, ProductRepository $repo, \Swift_Mailer $mailer)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setCoins(2000);
            $user->setLevel(1);
            $user->setAccountActive(0);
            $user->setXp(0);
            $user->setBackgroundActive(0);
            $user->setActiveLoadBackground(0);
            $user->setSkinActive(1);
            $user->addAchat($produits = $repo->find(1));
            $user->addAchat($produits = $repo->find(1));
            $manager->persist($user);
            $manager->flush();

            $message = (new \Swift_Message('Bienvenue chez ShipQuest'))
                ->setFrom('Welcome@Shipquest.fr')
                ->setTo($user->getEmail())
                ->setContentType("text/html")
                ->setBody(
                    $this->renderView(
                        'email/registrationmail.html.twig'
                    )
                )
            ;
            $mailer->send($message);


            return $this->redirectToRoute('security_login');

        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(){


        return $this->render('security/login.html.twig');

    }
    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(){

    }

    /**
     * @Route("/password", name="security_password")
     */
    public function passwordLost(){
        return $this->render('security/password.html.twig');
    }
    /**
     * @Route("/passwordOk", name="security_passwordOk")
     */
    public function passwordSend(UserRepository $repository,  \Swift_Mailer $mailer, ObjectManager $manager,UserPasswordEncoderInterface $encoder){


        $user = $repository->findOneBy(array('email'=> $_POST['_username']));
        if(!$user){
            $data = "Aucun utilisateur avec le mail";
        }else {
            $randomString = '';
            $allowedCharacters='0123456789abcdefgh';
            for (
                $i = 0, $allowedMaxIdx = mb_strlen($allowedCharacters) - 1;
                $i < 10;
                $i ++
            ) {
                $randomString .= $allowedCharacters[random_int(0, $allowedMaxIdx)];
            }
            $hash = $encoder->encodePassword($user, $randomString);
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            $message = (new \Swift_Message('Bienvenue chez ShipQuest'))
                ->setFrom('MDP@Shipquest.fr')
                ->setTo($user->getEmail())
                ->setContentType("text/html")
                ->setBody(
                    $this->renderView(
                        'email/password.html.twig', ['user' => $user, 'password' =>$randomString ]
                    )
                );
            $mailer->send($message);
            $data  = "Votre mot de passe à été envoyé a l'adresse suivante : " . $user->getEmail();

        }
        return $this->render('security/passwordSend.html.twig' , [
            'user' => $user,
            'data' => $data
        ]);
    }
    /**
     * @Route("/profile", name="security_profil")
     */
    public function profil( \Swift_Mailer $mailer){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $UserSkin = new ArrayCollection();
        $UserBG = new ArrayCollection();


        foreach ($user->getAchat() as $data){
                if($data->getCategory()->getName() == "Background"){
                    $UserBG->add($data);
                }
                else{
                    $UserSkin->add($data);
                }
            }

        return $this->render('security/profil.html.twig',[
            'user' => $user,
            'backgrounds' => $UserBG,
            'skins' => $UserSkin
        ]);

    }
}
