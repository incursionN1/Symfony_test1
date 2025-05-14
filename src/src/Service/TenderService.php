<?php
namespace App\Service;

use App\Entity\Status;
use App\Entity\Tenders;
use App\Repository\TendersRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class TenderService{
    public function __construct(
        private TendersRepository $tendersRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private PaginatorInterface $paginator,
    )
    {}

    public function getAllTenders(array $filters, int $page, int $limit): array
    {
        $query = $this->tendersRepository->createQueryBuilder('t');

        // Применяем фильтры
        if (!empty($filters['name'])) {
            if (!is_string($filters['name'])) {
                throw new BadRequestHttpException('Invalid name. Название тендера должно быть строкой');
            }
            $query->andWhere('t.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['date_from'])) {
            $dateFrom = DateTime::createFromFormat('d.m.Y H:i:s', $filters['date_from']);
            if (!$dateFrom) {
                throw new BadRequestHttpException('Invalid date_from format. Ожидаемый формат: d.m.Y H:i:s');
            }
            $query->andWhere('t.date_update >= :date_from')
                ->setParameter('date_from', $dateFrom);
        }

        if (!empty($filters['date_to'])) {
            $dateTo = DateTime::createFromFormat('d.m.Y H:i:s', $filters['date_to']);
            if (!$dateTo) {
                throw new BadRequestHttpException('Invalid date_to format. Ожидаемый формат: d.m.Y H:i:s');
            }
            $query->andWhere('t.date_update <= :date_to')
                ->setParameter('date_to', $dateTo);
        }

        $pagination = $this->paginator->paginate(
            $query->getQuery(),
            $page,
            $limit
        );

        return [
            'items' => $pagination->getItems(),
            'pagination' => [
                'current_page' => $pagination->getCurrentPageNumber(),
                'total_pages' => (int) ceil($pagination->getTotalItemCount() / $limit),
                'total_items' => $pagination->getTotalItemCount(),
                'items_per_page' => $limit,
            ]
        ];
    }

    public function getTender(int $id): ?Tenders
    {
        return $this->tendersRepository->find($id);
    }

    public function createTender(array $data): Tenders
    {
        try {
            $tender = $this->serializer->deserialize(
                json_encode($data),
                Tenders::class,
                'json',
                ['groups' => ['tender_write']]
            );

            if (isset($data['status'])) {
                $status = $this->entityManager->getReference(Status::class, $data['status']);
                $tender->setStatus($status);
            }

            $this->entityManager->persist($tender);
            $this->entityManager->flush();

            return $tender;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to create tender: ' . $e->getMessage());
        }
    }
}