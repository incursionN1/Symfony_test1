<?php

namespace App\Controller;

use App\Entity\Status;
use App\Entity\Tenders;
use App\Repository\TendersRepository;
use App\Service\CommonFunctionsService;
use App\Service\TenderService;
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
    public function __construct(
        private TenderService                   $tenderService,
        private SerializerInterface             $serializer,
        private readonly CommonFunctionsService $commonFunction,

    ) {}

    #[Route('/api/rest/tenders', name: 'app_api_rest_tenders_get_all', methods: ['GET'])]
    public function getAllTenders(Request $request): JsonResponse
    {
        try {
            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', 10);

            if ($page < 1 || $limit < 1) {
                throw new BadRequestHttpException('Page and limit must be greater than 0');
            }

            $filters = [
                'name' => $request->query->get('name'),
                'date_from' => $request->query->get('date_from'),
                'date_to' => $request->query->get('date_to'),
            ];

            $result = $this->tenderService->getAllTenders($filters, $page, $limit);
            $data = $this->serializer->serialize($result, 'json');

            return $this->createJsonResponse($result,);
        } catch (BadRequestHttpException $e) {
            return $this->commonFunction->handleError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            return $this->commonFunction->handleError($e->getMessage());
        }
    }

    #[Route('/api/rest/tenders/{id}', name: 'app_api_rest_tenders_get', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getTenders(int $id): JsonResponse
    {
        try {
            $tender = $this->tenderService->getTender($id);

            if (!$tender) {
                return $this->commonFunction->handleError('Tender not found',Response::HTTP_BAD_REQUEST);
            }
            return $this->createJsonResponse($tender);
        } catch (Throwable $e) {
            return  $this->commonFunction->handleError($e->getMessage());
        }
    }

    #[Route('/api/rest/tenders/', name: 'app_api_rest_tenders_post', methods: ['POST'])]
    public function addTenders(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return  $this->commonFunction->handleError( 'Invalid JSON data');
            }
            $tender = $this->tenderService->createTender($data);

            return $this->createJsonResponse($tender, Response::HTTP_CREATED);
        } catch (\RuntimeException $e) {
            return  $this->commonFunction->handleError($e->getMessage());
        } catch (Throwable $e) {
            return  $this->commonFunction->handleError($e->getMessage());
        }
    }
    private function createJsonResponse(
        $data,
        int $status = Response::HTTP_OK,
        array $serializerGroups = ['groups' => 'tender_get']
    ): JsonResponse {
        $serialized = $this->serializer->serialize($data, 'json', $serializerGroups);
        return new JsonResponse($serialized, $status, [], true);
    }
}