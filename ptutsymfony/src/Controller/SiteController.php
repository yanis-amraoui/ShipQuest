<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ArticleType;
class SiteController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function index(ArticleRepository $repo)
    {
        $article = $repo->findAll();


        return $this->render('site/index.html.twig', [
            'articles' => $article
        ]);
    }

    /**
     * @Route("/", name="site")
     */
    public function home()
    {
        $pdfPath = $this->getParameter('dir.downloads').'/sample.pdf';
        return $this->render('site/home.html.twig');
    }

    /**
     * @Route("/site/new", name="site_create")
     * @Route("/site/{id}/edit", name="article_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager)
    {
        if(!$article){
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('site');
        }

        return $this->render('site/create.html.twig', [
            'formarticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/site/{id}", name="site_show")
     */
    public function show(Article $article){
        return $this->render('site/show.html.twig',[
            'article' => $article
        ]);
    }

    /**
     * @Route("/contact", name="site_contact")
     */
    public function contact(){
        return $this->render('site/contact.html.twig');
    }
    /**
     * @Route("/game", name="game")
     */
    public function game()
    {
        return $this->render('site/game.html.twig');
    }


    /**
     * @Route("/ptut/web", name="ptut_web")
     */
    public function web()
    {
        return $this->render('site/ptutweb.html.twig');
    }

    /**
     * @Route("/ptut/game", name="ptut_game")
     */
    public function info()
    {
        return $this->render('site/ptutgame.html.twig');
    }


    /**
     * @Route("/about", name="site_about")
     */
    public function about(){
        return $this->render('site/about.html.twig');
    }
}
