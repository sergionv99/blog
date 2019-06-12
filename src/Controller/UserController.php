<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/perfil.html.twig');
    }

    /**
     * @Route("/register",name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        //nuevo usuario
        $user = new User();
        //le establecemos el rol que tendra  al registrarse (puede tenr mas de 1)
        $user->setRoles(['ROLE_USER']);
        //marca el usuario activo o inactivo
        $user->setIsActive(true);
        //creamos el formulario
        $form=$this->createForm(UserType::class,$user);
        //le pasamos la peticion
        $form->handleRequest($request);
        $error=$form->getErrors();
        //comprobamos que el formulario se en vie y sea validoo
        if($form->isSubmitted() && $form->isValid()){
            //encriptamos el password y lo guardamos como campo
            $password=$passwordEncoder->encodePassword($user,$user->getPlainPassword());
            $user->setPassword($password);//si modifica el campo $user, el que irá a la bd

            //con esto manejamos la entidades del doctrine
            $entityManager=$this->getDoctrine()->getManager();
            //con persist guardamos la info del formulario
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success','User created'
            );
            return $this->redirectToRoute('app_homepage');
        }
        //renderizar formulario
        return $this->render('user/regform.html.twig',[
            'error'=>$error,
            //'form' es el nombre para construir el formulario en la plantilla
            'form'=>$form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @Route("/login",name="app_login")
     */
    public function login(Request $request, AuthenticationUtils $authUtils){
        $error=$authUtils->getLastAuthenticationError();//guardaremos el último errore de la autentificación
        //last username
        $lastUsername=$authUtils->getLastUsername();
        return $this->render('user/login.html.twig',[
            'error'=>$error,
            'last_username'=>$lastUsername
        ]);
    }

    /**
     * Perfil de user en proceso
     * @Route("/perfil", name="view_profoile")
     */
    public function edit_user_profile(Request $request){
        $user = $this->getUser();
        $id = $user->getId();
        var_dump($id);
        die;
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        return $this->render('user/perfil.html.twig',[
            'post'=>$post
            ]);
    }

    /**
     * Función para listar mis posts
     * @Route("/uposts", name="user_posts")
     */
    public function misPosts(){
        $user = $this->getUser();
        $id = $user->getId();
        $posts=$this->getDoctrine()->getRepository(Post::class)->findBy(array('user'=>$id));
        return $this->render('user/userpost.html.twig',[
            'posts'=>$posts,
            'user'=>$id]);
    }

    /**
     * Función para eliminar producto - fruta
     * @Route("/uposts/{id}", name="publicar_p")
     */
    public function publicar_ps($id, Request $request)
    {
        $user=$this->getUser();

        return $this->publicar_post($request, $id, 'user_posts');
    }


    /**
     * Función para eliminar producto - fruta
     * @Route("/prueba", name="prrr")
     */
    public function publicar_ptest()
    {
        $user = $this->getUser();
        var_dump($user);
        die;
        return $this->publicar_post($request, $id, 'user_posts');
    }


    /**
     * Función para quitar la oferta
     * @param Request $request
     * @param int $id
     * @param string $route
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    private function publicar_post(Request $request, int $id, string $route){
        $post=$product=$this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        $postpu=$post[0];
        $entityManager=$this->getDoctrine()->getManager();
        $now = new \DateTime();
        $postpu->setCreatedAt($now);
        $entityManager->persist($postpu);
        $entityManager->flush();
        $this->addFlash('success', 'Oferta eliminado correctmanete');
        //una vez eliminado,volvemos a la página que indicamos por parámetros, para comprobar que se ha borrado correctamente
        return $this->redirectToRoute($route);
    }
    /**
     * Función para deslogarse
     * @Route("/logout",name="app_logout")
     */
    public function logout(){
        $this->redirectToRoute('/');
    }
}

