<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use App\Entity\{Book, BookTranslate, Author};
use App\Validator\Book\PostRequest;

use App\Service\RequestChecker;
use App\Service\MySerializer;

class BookController extends AbstractController
{
    /**
     * @Route("/book/create/", name="bookCreate", methods={"POST"})
    */
    public function postReq(Request $request, RequestChecker $requestChecker, MySerializer $serializer): JsonResponse
    {
        $messages = [];

        try {
            $em = $this->getDoctrine()->getManager();
            $data = $requestChecker->validate($request->getContent(), PostRequest::class);

            if (!$author = $em->getRepository(Author::class)->find($data->getAuthorId())) 
                throw new BadRequestHttpException('Incorrect author');

            $item = new Book;
	    $item->setAuthor($author);

	    $item->translate('en')->setName($data->getNameEn());
	    $item->translate('ru')->setName($data->getNameRu());
            $item->mergeNewTranslations();

            $em->persist($item);
            $em->flush();
        } catch (\Throwable $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return (new JsonResponse())->setContent($serializer->getSerializer()->serialize($item, 'json', ['groups' => ['book']]))->setStatusCode(Response::HTTP_OK);
    }
}
