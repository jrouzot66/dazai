<?php

namespace App\Repository;

use App\Entity\Delivery;
use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Delivery>
 */
class DeliveryRepository extends ServiceEntityRepository implements DeliveryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Delivery::class);
    }

    /**
     * @return Delivery[]
     */
    public function findForOrganization(Organization $org): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.vendor = :org OR d.buyer = :org')
            ->setParameter('org', $org)
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}