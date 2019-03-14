<?php

namespace App\Controller\BackController;

use App\Entity\Comment;
use App\Repository\CommentRepository;
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
     */
    public function index()
    {
        $comments = $this->repository->findAll();

        return $this->render('admin/comment/home.html.twig', [
            'controller_name' => 'AdminCommentController',
            'current_menu' => 'adminComment',
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
