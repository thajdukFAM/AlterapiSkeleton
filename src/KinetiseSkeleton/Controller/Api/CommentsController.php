<?php

namespace KinetiseSkeleton\Controller\Api;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\SerializationContext;
use KinetiseSkeleton\Controller\AbstractController;
use KinetiseSkeleton\Doctrine\Entity\Comment;
use KinetiseSkeleton\Response\MessageResponse;
use KinetiseSkeleton\Response\Model\Rss;
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
            $this->getSerializer()->serialize(
                new Rss(new ArrayCollection($comments)),
                'xml',
                SerializationContext::create()->setSerializeNull(true)
            ),
            Response::HTTP_OK,
            array(
                'Content-Type' => 'application/xml; charset=utf-8'
            )
        );
    }

    public function commentAction($id)
    {
        $comment = $this
            ->getEntityManager()
            ->find('KinetiseSkeleton\Doctrine\Entity\Comment', $id)
        ;

        if (!$comment) {
            return new MessageResponse('Comment not found!', array(), Response::HTTP_BAD_REQUEST, array(
                'Content-Type' => 'application/xml, charset=UTF-8'
            ));
        }

        return new Response(
            $this->getSerializer()->serialize(
                new Rss(new ArrayCollection(array($comment))),
                'xml',
                SerializationContext::create()->setSerializeNull(true)
            ),
            Response::HTTP_OK,
            array(
                'Content-Type' => 'application/xml; charset=utf-8'
            )
        );
    }

    public function addAction(Request $request)
    {
        $json = $request->request->get('_json', array());

        if (!array_key_exists('items', $json) || !array_key_exists('form', $json['items'][0])) {
            return new MessageResponse('Bad Request', array(), Response::HTTP_BAD_REQUEST, array(
                'Content-Type' => 'application/xml, charset=UTF-8'
            ));
        }

        $data = $json['items'][0]['form'];

        if (!array_key_exists('author', $data) || !array_key_exists('message', $data)) {
            return new MessageResponse('Data missing', array(), Response::HTTP_BAD_REQUEST, array(
                'Content-Type' => 'application/xml, charset=UTF-8'
            ));
        }

        $comment = new Comment();
        $comment->setAuthor(trim($data['author']));
        $comment->setMessage(trim($data['message']));

        $this->getEntityManager()->persist($comment);
        $this->getEntityManager()->flush();

        return new MessageResponse(
            null,
            array(
                $this->getUrlGenerator()->generate('api_comments', array(), UrlGeneratorInterface::ABSOLUTE_URL)
            ),
            Response::HTTP_OK,
            array(
                'Content-Type' => 'application/xml, charset=UTF-8'
            )
        );
    }

    public function deleteAction($id)
    {
        $em = $this->getEntityManager();

        $comment = $em->find('KinetiseSkeleton\Doctrine\Entity\Comment', $id);

        if (!$comment) {
            return new MessageResponse('Comment not found!', array(), Response::HTTP_BAD_REQUEST, array(
                'Content-Type' => 'application/xml, charset=UTF-8'
            ));
        }

        $em->remove($comment);
        $em->flush();

        return new MessageResponse(
            null,
            array(
                $this->getUrlGenerator()->generate('api_comments', array(), UrlGeneratorInterface::ABSOLUTE_URL)
            ),
            Response::HTTP_OK,
            array(
                'Content-Type' => 'application/xml, charset=UTF-8'
            )
        );
    }
}