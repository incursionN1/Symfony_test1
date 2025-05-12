<?php

namespace App\Controller;

use App\Entity\Status;
use App\Entity\Tenders;
use App\Repository\TendersRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;


final class ApiRestTendersController extends AbstractController
{
    #[Route('/api/rest/tenders', name: 'app_api_rest_tenders_get_all', methods: ['GET'],)]
    public function getAllTenders(TendersRepository $tendersRepository, SerializerInterface $serializer, Request $request,PaginatorInterface $paginator): JsonResponse
    {
        try {
            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', 10);
            if (!is_int($page) || $page < 1) {
                throw new BadRequestHttpException('Invalid page. Страница должны быть числом больше 0 ');
            }
            if (!is_int($limit) || $limit < 1) {
                throw new BadRequestHttpException('Invalid limit. Страница должны быть числом больше 0 ');
            }
            $query = $tendersRepository->createQueryBuilder('t');
            if ($nameFilter = $request->query->get('name')) {
                if (!is_string($nameFilter)) {
                    throw new BadRequestHttpException('Invalid name. Название тендера должно быть строкой');
                }
                $query->andWhere('t.name LIKE :name')
                    ->setParameter('name', '%' . $nameFilter . '%');
            }
            if ($dateFromFilter = $request->query->get('date_from')) {
                $dateFrom = DateTime::createFromFormat('d.m.Y H:i:s', $dateFromFilter);
                if (!$dateFrom) {
                    throw new BadRequestHttpException('Invalid date_from format. Ожидаемый формат: d.m.Y H:i:s');
                }
                $query->andWhere('t.date_update >= :date_from')
                    ->setParameter('date_from', $dateFrom);
            }
            if ($dateToFilter = $request->query->get('date_to')) {
                $dateTo = DateTime::createFromFormat('d.m.Y H:i:s', $dateToFilter);
                if (!$dateTo) {
                    throw new BadRequestHttpException('Invalid date_to format. Ожидаемый формат: d.m.Y H:i:s');
                }
                $query->andWhere('t.date_update <= :date_to')
                    ->setParameter('date_to', $dateTo);
            }
            $pagination = $paginator->paginate(
                $query->getQuery(),
                $page,
                $limit
            );
            $data = [
                'items' => $pagination->getItems(),
                'pagination' => [
                    'current_page' => $pagination->getCurrentPageNumber(),
                    'total_pages' => (int) ceil($pagination->getTotalItemCount() / $limit),
                    'total_items' => $pagination->getTotalItemCount(),
                    'items_per_page' => $limit,
                ]
            ];
            $data = $serializer->serialize($data, 'json');
            return new JsonResponse($data, Response::HTTP_OK, [], true);

        } catch (BadRequestHttpException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (Throwable $e) {
            return new JsonResponse(
                ['error' => 'Internal server error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    #[Route('/api/rest/tenders/{id}', name: 'app_api_rest_tenders_get', requirements: ['id' => '\d+'], methods: ['GET'],)]
    public function getTenders(TendersRepository $tendersRepository, SerializerInterface $serializer,$id): JsonResponse
    {
        $data = $serializer->serialize( $tendersRepository->find($id), 'json', ['groups' => 'tender_get']);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
    #[Route('/api/rest/tenders/', name: 'app_api_rest_tenders_post', methods: ['POST'],)]
    public function addTenders(EntityManagerInterface $entityManager, SerializerInterface $serializer, Request $request): JsonResponse
    {

        try {
            $tender = $serializer->deserialize(
                $request->getContent(),
                'App\Entity\Tenders',
                'json',
                ['groups' => ['tender_write']]
            );
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Invalid JSON data'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['status'])) {
            $status = $entityManager->getReference(Status::class, $data['status']);
            $tender->setStatus($status);
        }
        try {
            $entityManager->persist($tender);
            $entityManager->flush();
            $data = $serializer->serialize($tender, 'json', ['groups' => 'tender_get']);

            return new JsonResponse($data, Response::HTTP_CREATED, [], true);
        }catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Internal server error', 'exception' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
