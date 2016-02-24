<?php

namespace KinetiseSkeleton\Controller\Api;

use KinetiseSkeleton\Controller\AbstractController;
use KinetiseSkeleton\Doctrine\Entity\Comment;
use KinetiseSkeleton\Response\MessageResponse;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CommentsController extends AbstractController
{
    public function getAction()
    {
        $comments = $this
            ->getEntityManager()
            ->getRepository('KinetiseSkeleton\Doctrine\Entity\Comment')
            ->findAll();

        return new Response(
            json_encode(array("results" => json_decode($this->getSerializer()->serialize($comments, 'json')))),
            Response::HTTP_OK,
            array('Content-Type' => 'application/json; charset=utf-8')
        );
    }

    public function commentAction($id)
    {
        $comment = $this
            ->getEntityManager()
            ->find('KinetiseSkeleton\Doctrine\Entity\Comment', $id)
        ;

        if (!$comment) {
            return new MessageResponse('Error', 'Comment not found!', Response::HTTP_BAD_REQUEST);
        }

        return new Response(
            $this->getSerializer()->serialize($comment, 'json'),
            Response::HTTP_OK,
            array('Content-Type' => 'application/json; charset=utf-8')
        );
    }

    public function addAction(Request $request)
    {
        $data = $request->request->get('form', false);

        if (!$data) {
            return new MessageResponse('Error', 'Bad Request', Response::HTTP_BAD_REQUEST);
        }

        if (!array_key_exists('author', $data) || !array_key_exists('message', $data)) {
            return new MessageResponse('Error', 'Data missing', Response::HTTP_BAD_REQUEST);
        }

        $comment = new Comment();
        $comment->setAuthor(trim($data['author']));
        $comment->setMessage(trim($data['message']));

        $this->getEntityManager()->persist($comment);
        $this->getEntityManager()->flush();

        return new MessageResponse(
            "Comment added",
            $this->getUrlGenerator()->generate('api_comments', array(), UrlGeneratorInterface::ABSOLUTE_URL),
            Response::HTTP_OK
        );
    }

    public function deleteAction($id)
    {
        $em = $this->getEntityManager();

        $comment = $em->find('KinetiseSkeleton\Doctrine\Entity\Comment', $id);

        if (!$comment) {
            return new MessageResponse('Error', 'Comment not found!', Response::HTTP_BAD_REQUEST);
        }

        $em->remove($comment);
        $em->flush();

        return new MessageResponse(
            "Comment deleted",
            $this->getUrlGenerator()->generate('api_comments', array(), UrlGeneratorInterface::ABSOLUTE_URL),
            Response::HTTP_OK
        );
    }
}