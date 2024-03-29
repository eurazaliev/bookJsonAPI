<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use App\Entity\{Book, BookTranslate, Author};
use App\Validator\Book\{PostRequest, SearchRequest};

use App\Service\RequestChecker;
use App\Service\MySerializer;

class BookController extends AbstractController
{
    /**
     * @Route("/book/create/", name="bookCreate", methods={"POST"})
    */
    public function postCreate(Request $request, RequestChecker $requestChecker, MySerializer $serializer): JsonResponse
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
        return (new JsonResponse())->setContent($serializer->getSerializer()->serialize($item, 'json', ['groups' => ['book']]))->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @Route("/book/search/", name="bookSearch", methods={"POST"})
    */
    public function getSearch(Request $request, RequestChecker $requestChecker, MySerializer $serializer, PaginatorInterface $paginator): JsonResponse
    {
        try {
            /*
            тут я не буду в тестовом задании делать полноценный поисковый движок
            ищет тупо по LIKE, выдает ограниченный набор данных с пейждинацией
            можно указать нужную страницу и сколько айтемов на странице
            по умолчанию - 5 поз. на странице и показываем страницу 1
            */

            $em = $this->getDoctrine()->getManager();
            $data = $requestChecker->validate($request->getContent(), SearchRequest::class);

            $qb = $em->createQueryBuilder();
            $qb->select('bt')
                ->from('App\Entity\BookTranslation', 'bt')
                ->where('bt.name LIKE :query')
            ;
            $qb->setParameter('query', '%' . $data->getQueryString() . '%');

            $pagination = $paginator->paginate(
                $qb,
                $data->getPage(),
                $data->getItemsPerPage()
            );

            $item = $pagination->getItems();
            $result = [
                'item' => $item,
                'itemsinset' => $pagination->count(),
                'page' => $pagination->getCurrentPageNumber(),
                'itemsperpage' => $pagination->getItemNumberPerPage(),
                'itemstotal' => $pagination->getTotalItemCount(),
                'exp' => $pagination->getPaginationData(),
            ];
            if($pagination->getTotalItemCount() === 0) {
                $status = Response::HTTP_NOT_FOUND;
            } else {
                $status = $pagination->getPaginationData()['pageCount'] > 1 ? Response::HTTP_PARTIAL_CONTENT : Response::HTTP_OK;
            }

        } catch (\Throwable $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return (new JsonResponse())->setContent($serializer->getSerializer()->serialize($result, 'json', ['groups' => ['book']]))->setStatusCode($status);
    }

    /**
     * @Route("/{lang}/book/{id}/", name="bookGetByLang", methods={"GET"}, defaults={"lang" = "en"}, requirements={"lang" = "en|ru", "id"="\d+"})
     */
    public function getReq(int $id, string $lang, Request $request, RequestChecker $requestChecker, MySerializer $serializer, PaginatorInterface $paginator): JsonResponse
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $item = $em->getRepository(Book::class)->find($id);
            if(!$item)
                throw new BadRequestHttpException('Item not found: ' . $id);

            $result = new \StdClass();
            $result->id = $item->getId();
            if(!empty($name = $item->translate($lang)->getName()))
                $result->Name = $name;
            else $result->Name = 'We are sorry but there is no title in ' . $lang . ' for the book: ' . $id . '.';
        } catch (\Throwable $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
        return (new JsonResponse())->setContent($serializer->getSerializer()->serialize($result, 'json'))->setStatusCode(Response::HTTP_OK);
    }
}
