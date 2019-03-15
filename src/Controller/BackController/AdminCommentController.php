<?php

namespace App\Controller\BackController;

use App\Entity\Comment;
use App\Entity\PostSearch;
use App\Form\PostSearchType;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{

    /**
     * @var CommentRepository
     */
    private $repository;

    /**
     * AdminCommentController constructor.
     * @param CommentRepository $repository
     */
    public function __construct(CommentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/admin/comment", name="admin.comment.home")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $search = new PostSearch();
        $form = $this->createForm(PostSearchType::class, $search);
        $form->handleRequest($request);

        $comments = $paginator->paginate(
            $this->repository->findAllOrderSignal($search),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/comment/home.html.twig', [
            'controller_name' => 'AdminCommentController',
            'current_menu' => 'adminComment',
            'form' => $form->createView(),
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin.comment.edit")
     * @param Comment $comment
     * @return Response
     */
    public function edit(Comment $comment): Response
    {
        return $this->render('admin/comment/edit.html.twig', [
            'current_menu' => 'adminComment',
            'comment' => $comment
        ]);
    }

    /**
     * @Route("/{id}", name="admin.comment.delete", methods={"DELETE"})
     * @param Request $request
     * @param Comment $comment
     * @return Response
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Votre commentaire a bien été supprimée');
        }

        return $this->redirectToRoute('admin.comment.home');
    }
}
