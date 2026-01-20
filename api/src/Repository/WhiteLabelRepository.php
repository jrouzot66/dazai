<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Repository;

use App\Entity\WhiteLabel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WhiteLabel>
 */
class WhiteLabelRepository extends ServiceEntityRepository implements WhiteLabelRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WhiteLabel::class);
    }

    public function findOneByDomainUrl(string $domainUrl): ?WhiteLabel
    {
        return $this->findOneBy(['domainUrl' => $domainUrl]);
    }
}