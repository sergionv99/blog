<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use App\Form\PostType;
use App\Form\EditPostType;
class HomeController extends AbstractController
{

    /**
     * @Route("/", name="app_homepage")
     */
    public function index()
    {
            $posts=$this->getDoctrine()->getRepository(Post::class)->findAll();
        return $this->render('home/index.html.twig', [
            'post' => $posts
        ]);
    }

    /**
     * @Route("/test", name="testas")
     */
    public function test(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(EditPostType::class, $post);
        $form->handleRequest($request);
        $error = $form->getErrors();
        if ($form->isSubmitted() && $form->isValid()) {
            //$time = date('Y-m-d H:i:s');
            $now = new \DateTime();
            $post = $form->getData();
            $post->setCreatedAt($now);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            //flush to DB
            $entityManager->flush();
            $this->addFlash('success', 'Post creado correctamente');
            return $this->redirectToRoute('app_homepage');
        }
        return $this->render('home/test.html.twig', [
            'error'=>$error,
            'form' => $form->createView()
        ]);

    }
}
