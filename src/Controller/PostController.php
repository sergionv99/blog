<?php

namespace App\Controller;

use App\Form\ComentarType;
use App\Form\EditPostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\PostType;
use Symfony\Component\Validator\Constraints\DateTime;


class PostController extends AbstractController
{
    /**
     * @Route("/post", name="app_postpage")
     */
    public function index()
    {
        $posts=$this->getDoctrine()->getRepository(Post::class)->findAll();
        return $this->render('post/index.html.twig', [
            'post' => $posts
        ]);
    }

    /**
     * Función para ver el post
     * @Route("post/{id}", name="viewpost")
     */
    public function viewPost($id){
       // $esto = $this->index2($id);
        return $this->index2($id, 'post/vista.html.twig',[
        ]);
    }

    /**
     * @param $id
     * @param string $template
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function index2($id)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        $com = new Comment();
       // $form= $this->createForm(ComentarType::class, $com);

        $usuario=$this->getUser();

        $comentarios = $this->getDoctrine()->getRepository(Comment::class)->findBy(array('post'=>$post));
        return $this->render('post/vista.html.twig', [
            'post' => $post,
            'comentarios' => $comentarios,
            //'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/post/newcomment/{id}", name="new_coment")
     */
    public function newComment($id, Request $request)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        $comentario = new Comment();

        $form= $this->createForm(ComentarType::class, $comentario);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $usuario=$this->getUser();
            $comentario->setUser($usuario);
        $comentario->setPost($post);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comentario);
        $entityManager->flush();
       // $route = "/post/".$id;
            return $this->redirectToRoute('app_postpage');
    }
        return $this->render('post/comment.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/postnew", name="new_post")
     */
    public function newPost(Request $request)
    {
        $user = $this->getUser();
        $post = new Post();
        $post->setUser($user);
        $author = $user->getUsername();
        $post->setAuthor($author);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $error = $form->getErrors();
        if ($form->isSubmitted() && $form->isValid()) {
            //$time = date('Y-m-d H:i:s');
            $now = new \DateTime();
            $post = $form->getData();
            $comprobar = $post->getIspublished();
            if ($comprobar != null){
                $post->setPublishedAt($now);
            }
            $post->setCreatedAt($now);
            $post->setModifiedAt($now);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        //flush to DB
        $entityManager->flush();
        $this->addFlash('success', 'Post creado correctamente');
        return $this->redirectToRoute('app_homepage');
    }
        return $this->render('post/post.html.twig', [
            'error'=>$error,
            'form' => $form->createView()
        ]);

    }

    /**
     * Función para ver cada post ->Botón Leer más
     * @Route("/postu/{id}", name="view_post23")
     */
    public function ind(Request $request,$id)
    {
        $user = $this->getUser();
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        $comentarios = $this->getDoctrine()->getRepository(Comment::class)->findBy(array('post' => $post));

        return $this->render('post/vista.html.twig', [
            'user' => $user,
            'post' => $post,
            'comments' => $comentarios
        ]);
    }

    /**
     * Función para eliminar producto - fruta
     * @Route("/post/publicar/{id}", name="publicar_post")
     */
    public function publicar_ps($id, Request $request)
    {
        return $this->publicar_post($request, $id, 'app_postpage');
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
        $postu=$post[0];
        $now = new \DateTime();
        $postu->setIspublished(1);
        $postu->setPublishedAt($now);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->persist($postu);
        $entityManager->flush();
        $this->addFlash('success', 'Oferta eliminado correctmanete');
        //una vez eliminado,volvemos a la página que indicamos por parámetros, para comprobar que se ha borrado correctamente
        return $this->redirectToRoute($route);
    }

    /**
     * Función para eliminar posts
     * @Route("/post/delete/{id}", name="delete_post")
     */
    public function deletePost($id, Request $request)
    {
        return $this->deletePostPri($request, $id, 'app_postpage');
    }

    /**
     * Función para eliminar posts
     * @Route("/post/delete/{id}", name="user_posts")
     */
    public function deletePostUser($id, Request $request)
    {
        return $this->deletePostPri($request, $id, 'app_postpage');
    }

    /**
     * Función para eliminar producto
     * @param Request $request
     * @param int $id
     * @param string $route
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function deletePostPri(Request $request, int $id, string $route){
        //buscamos por el id del producto que hemos seleccionado para eliminar
        $post=$this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        $postdel=$post[0];
        $entityManager=$this->getDoctrine()->getManager();
        //comando en cuestión que borrará el producto
        $entityManager->remove($postdel);
        $entityManager->flush();
        $this->addFlash('success', 'Post eliminado');
        //una vez eliminado,volvemos a la página que indicamos por parámetros, para comprobar que se ha borrado correctamente
        return $this->redirectToRoute($route);
    }
    /**
     * Función para eliminar producto - fruta
     * @Route("/post/desp/{id}", name="a_despublicar")
     */
    public function despublicarpost($id, Request $request)
    {
        return $this->despublicar($request, $id, 'user_posts');
    }
    /**
     * Función para despublicar
     * @param Request $request
     * @param int $id
     * @param string $route
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function despublicar(Request $request, int $id, string $route){
        $post=$this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        $postdes=$post[0];
        $entityManager=$this->getDoctrine()->getManager();
        $postdes->setIspublished(0);
        $entityManager->persist($postdes);
        $entityManager->flush();
        $this->addFlash('success', 'Oferta eliminado correctmanete');
        //una vez eliminado,volvemos a la página que indicamos por parámetros, para comprobar que se ha borrado correctamente
        return $this->redirectToRoute($route);
    }

    /**
     * Función para editar productos - frutas
     * @Route("post/edit_post/{id}", name="edit_post")
     */

    public function editPost__($id,Request $request){
        return $this->editPost($id, $request, 'post/edit_post.html.twig', 'app_postpage');
    }
    /**
     * Función para editar productos
     * @param int $id
     * @param Request $request
     * @param string $template
     * @param string $route
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function editPost(int $id, Request $request, string $template, string $route){
        $post=$this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        $postedit=$post[0];
        $form=$this->createForm(EditPostType::class,$postedit);
        $form->handleRequest($request);
        $error=$form->getErrors();
        if($form->isSubmitted() && $form->isValid()){
            $now = new \DateTime();
            $postedit->setModifiedAt($now);
            $postedit=$form->getData();
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Producto modificado correctamente');
            return $this->redirectToRoute($route);
        }
        return $this->render($template,[
            'error'=>$error,
            'post'=>$post,
            'form'=>$form->createView()
        ]);
    }


}
