<?php

namespace App\Controller\BackController;


use App\Entity\Post;
use App\Entity\PostSearch;
use App\Form\PostSearchType;
use App\Form\PostType;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminPostController extends AbstractController
{
    /**
     * @var PostRepository
     */
    private $repository;
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * AdminPostController constructor.
     * @param PostRepository $repository
     * @param ObjectManager $em
     */
    public function __construct(PostRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin.post.home")
     * @return Response
     */
    public function homeAdmin(PaginatorInterface $paginator, Request $request): Response
    {
        $search = new PostSearch();
        $form = $this->createForm(PostSearchType::class, $search);
        $form->handleRequest($request);

        $posts = $paginator->paginate(
            $this->repository->findAllDescQuery($search),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('admin/post/home.html.twig', [
            'posts' => $posts,
            'form' => $form->createView(),
            'current_menu' => 'adminPost',
        ]);
    }

    /**
     * @Route("admin/post/create", name="admin.post.new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->em->persist(($post));
            $this->em->flush();
            $this->addFlash('success', 'Votre article a bien été créé');
            return $this->redirectToRoute('admin.post.home');
        }

        return $this->render('admin/post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'current_menu' => 'adminPost',
        ]);
    }

    /**
     * @Route("/admin/post/{id}", name="admin.post.edit", methods="GET|POST")
     * @param Post $post
     * @param Request $request
     * @return Response
     */
    public function edit(Post $post, Request $request): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->em->flush();
            $this->addFlash('success', 'Votre article a bien été modifié');
            return $this->redirectToRoute('admin.post.home');
        }

        return $this->render('admin/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'current_menu' => 'adminPost',
        ]);
    }

    /**
     * @Route("/admin/post/{id}", name="admin.post.delete", methods="DELETE")
     * @param Post $post
     * @param Request $request
     * @return Response
     */
    public function delete(Post $post, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->get('_token'))){
            $this->em->remove($post);
            $this->em->flush();
            $this->addFlash('success', 'Votre article a bien été supprimé');
        }

        return $this->redirectToRoute('admin.post.home');
    }
}
