<?php

namespace App\Repository;

use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Trip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trip[]    findAll()
 * @method Trip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }


    public function findAvailableTrips($filter = '', $priceOrder = '', $titleOrder = '', $locationOrder = '', $priceFrom = '', $priceTo = '')
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->select('t')
            ->andWhere($queryBuilder->expr()->isNotNull('t.vacant_spaces'));
        if ($filter) {
            $queryBuilder->andWhere('t.title LIKE :filter OR t.description LIKE :filter')
                ->setParameter('filter', '%' . $filter . '%');
        }
        if($priceFrom) {
            $queryBuilder->andWhere('t.price >= :priceFrom')
                ->setParameter('priceFrom', $priceFrom);
        }
        if($priceTo) {
            $queryBuilder->andWhere('t.price <= :priceTo')
                ->setParameter('priceTo', $priceTo);
        }
        if ($priceOrder) {
            $queryBuilder->orderBy('t.price', $priceOrder);
        }
        if ($titleOrder) {
            $queryBuilder->orderBy('t.title', $titleOrder);
        }
        if ($locationOrder) {
            $queryBuilder->orderBy('t.location', $locationOrder);
        }

        $result = $queryBuilder->getQuery()
            ->getResult();
        return $result;
    }


}
