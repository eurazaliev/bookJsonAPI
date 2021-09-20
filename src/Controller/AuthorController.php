<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use App\Entity\Author;
use App\Validator\Author\PostRequest;

use App\Service\RequestChecker;
use App\Service\MySerializer;

class AuthorController extends AbstractController
{
    /**
     * @Route("/author/create/", name="authorCreate", methods={"POST"})
    */
    public function postReq(Request $request, RequestChecker $requestChecker, MySerializer $serializer): JsonResponse
    {
        $messages = [];

        try {
            $em = $this->getDoctrine()->getManager();
            $data = $requestChecker->validate($request->getContent(), PostRequest::class);

            $item = new Author;
            $item->setName($data->getName());

            $em->persist($item);
            $em->flush();
        } catch (\Throwable $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return (new JsonResponse())->setContent($serializer->getSerializer()->serialize($item, 'json', ['groups' => ['author']]))->setStatusCode(Response::HTTP_CREATED);
    }

}
