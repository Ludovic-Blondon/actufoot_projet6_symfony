<?php

namespace App\Controller\FrontController;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Contact;
use App\Entity\Post;
use App\Entity\PostSearch;
use App\Form\CommentType;
use App\Form\ContactType;
use App\Form\PostSearchType;
use App\Notification\ContactNotification;
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
     * @param Request $request
     * @return Response
     */
    public function show(Post $post, string $slug, Request $request): Response
    {
        if ($post->getSlug() !== $slug){
            return $this->redirectToRoute('post.show', [
                'id' => $post->getId(),
                'slug' => $post->getSlug()
            ], 301);
        }

        $comment = new Comment();
        $comment->setPost($post);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Votre commentaire a bien été posté');

            return $this->redirectToRoute('post.show', [
                'id' => $post->getId(),
                'slug' => $post->getSlug()
            ]);
        }

        $categoriesNav = $this->categoryRepository->findAll();

        return $this->render('front/show.html.twig', [
            'current_menu' => 'allnews',
            'categories' => $categoriesNav,
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/categorie/{slug}-{id}", name="category", requirements={"slug": "[a-z0-9\-]*"})
     * @param Category $category
     * @param int $id
     * @param string $slug
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function showByCategory(Category $category, int $id, string $slug, PaginatorInterface $paginator, Request $request): Response
    {
        if ($category->getSlug() !== $slug){
            return $this->redirectToRoute('category', [
                'id' => $category->getId(),
                'slug' => $category->getSlug()
            ], 301);
        }

        $categoriesNav = $this->categoryRepository->findAll();

        $search = new PostSearch();
        $form = $this->createForm(PostSearchType::class, $search);
        $form->handleRequest($request);

        $posts = $paginator->paginate(
            $this->repository->findByCategory($id, $search),
            $request->query->getInt('page', 1),
            2
        );

        return $this->render('front/bycategory.html.twig', [
            'current_menu' => 'category',
            'categories' => $categoriesNav,
            'category' => $category,
            'posts' => $posts,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param ContactNotification $notification
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function contact(Request $request, ContactNotification $notification):Response
    {
        $categoriesNav = $this->categoryRepository->findAll();

        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $notification->notify($contact);
            $this->addFlash('success', 'Votre message a bien été envoyé');
            $this->redirectToRoute('contact');
            return $this->redirectToRoute('contact');
        }

        return $this->render('front/contact.html.twig', [
            'current_menu' => 'contact',
            'categories' => $categoriesNav,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/comment/{id}", name="comment.signal")
     * @param Comment $comment
     * @return Response
     */
    public function signalComment(Comment $comment): Response
    {
        $comment->setSignalCount( $comment->getSignalCount() + 1);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();
        $post = $comment->getPost();
        $slug = $post->getSlug();
        $postId = $post->getId();

        $this->addFlash('success', 'Votre signalement a bien été prit en compte merci');

        return $this->redirectToRoute('post.show', [
            'slug' => $slug,
            'id' => $postId
        ]);
    }
}
