<?php

namespace App\Repository;

use App\Entity\ItemCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ItemCollection>
 */
class ItemCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemCollection::class);
    }
    public function findLargestCollections(int $limit = 5): array
    {
        return $this->createQueryBuilder('c')
            ->select('c, COUNT(i.id) AS HIDDEN item_count')
            ->leftJoin('c.items', 'i')
            ->groupBy('c.id')
            ->orderBy('item_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
