<?php

namespace App\Controller\BackController;

use App\Entity\Category;
use App\Entity\PostSearch;
use App\Form\CategoryType;
use App\Form\PostSearchType;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/category")
 */
class AdminCategoryController extends AbstractController
{

    /**
     * @var CategoryRepository
     */
    private $repository;

    /**
     * AdminCategoryController constructor.
     * @param CategoryRepository $repository
     */
    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="admin.category.home", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $search = new PostSearch();
        $form = $this->createForm(PostSearchType::class, $search);
        $form->handleRequest($request);

        $categories = $paginator->paginate(
            $this->repository->findAllWithSearch($search),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/category/home.html.twig', [
            'categories' => $categories,
            'current_menu' => 'adminCategory',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="admin.category.new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin.category.home');
        }

        return $this->render('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'current_menu' => 'adminCategory',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin.category.edit", methods={"GET","POST"})
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin.category.home', [
                'id' => $category->getId(),
            ]);
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'current_menu' => 'adminCategory',
        ]);
    }

    /**
     * @Route("/{id}", name="admin.category.delete", methods={"DELETE"})
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.category.home');
    }
}
