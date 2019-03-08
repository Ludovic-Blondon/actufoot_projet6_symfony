<?php

namespace App\Controller\FrontController;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\PostSearch;
use App\Form\PostSearchType;
use App\Repository\CategoryRepository;
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
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * FrontPostController constructor.
     * @param PostRepository $repository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(PostRepository $repository, CategoryRepository $categoryRepository)
    {
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function home(): Response
    {
        $categoriesNav = $this->categoryRepository->findAll();
        $posts = $this->repository->findLatest();
        return $this->render('front/home.html.twig', [
            'current_menu' => 'home',
            'categories' => $categoriesNav,
            'posts' => $posts
        ]);

    }

    /**
     * @Route("/toutes-les-news", name="allNews")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function allNews(PaginatorInterface $paginator, Request $request): Response
    {
        $categoriesNav = $this->categoryRepository->findAll();

        $search = new PostSearch();
        $form = $this->createForm(PostSearchType::class, $search);
        $form->handleRequest($request);

        $posts = $paginator->paginate(
            $this->repository->findAllDescQuery($search),
            $request->query->getInt('page', 1),
            9
        );
        return $this->render('front/allnews.html.twig', [
            'current_menu' => 'allnews',
            'categories' => $categoriesNav,
            'posts' => $posts,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{slug}-{id}", name="post.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Post $post
     * @param string $slug
     * @return Response
     */
    public function show(Post $post, string $slug)
    {
        $categoriesNav = $this->categoryRepository->findAll();

        if ($post->getSlug() !== $slug){
            return $this->redirectToRoute('post.show', [
                'id' => $post->getId(),
                'slug' => $post->getSlug()
            ], 301);
        }
        return $this->render('front/show.html.twig', [
            'current_menu' => 'allnews',
            'categories' => $categoriesNav,
            'post' => $post
        ]);
    }

    /**
     * @Route("/categorie/{slug}-{id}", name="category", requirements={"slug": "[a-z0-9\-]*"})
     * @param Category $category
     * @return Response
     */
    public function showByCategory(Category $category): Response
    {
        $categoriesNav = $this->categoryRepository->findAll();

        return $this->render('front/bycategory.html.twig', [
            'current_menu' => 'category',
            'categories' => $categoriesNav,
            'category' => $category
        ]);
    }
}
