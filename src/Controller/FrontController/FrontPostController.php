<?php

namespace App\Controller\FrontController;

use App\Entity\Post;
use App\Entity\PostSearch;
use App\Form\PostSearchType;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontPostController extends AbstractController
{
    /**
     * @var PostRepository
     */
    private $repository;

    /**
     * FrontPostController constructor.
     * @param PostRepository $repository
     */
    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function home(): Response
    {
        $posts = $this->repository->findLatest();
        return $this->render('front/home.html.twig', [
            'current_menu' => 'home',
            'posts' => $posts
        ]);

    }

    /**
     * @Route("/toutes-les-news", name="allNews")
     * @return Response
     */
    public function allNews(PaginatorInterface $paginator, Request $request): Response
    {
        $search = new PostSearch();
        $form = $this->createForm(PostSearchType::class, $search);
        $form->handleRequest($request);

        $posts = $paginator->paginate(
            $this->repository->findAllDescQuery($search),
            $request->query->getInt('page', 1),
            3
        );
        return $this->render('front/allnews.html.twig', [
            'current_menu' => 'allnews',
            'posts' => $posts,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{slug}-{id}", name="post.show", requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function show(Post $post, string $slug)
    {
        if ($post->getSlug() !== $slug){
            return $this->redirectToRoute('post.show', [
                'id' => $post->getId(),
                'slug' => $post->getSlug()
            ], 301);
        }
        return $this->render('front/show.html.twig', [
            'current_menu' => 'allnews',
            'post' => $post
        ]);
    }
}
