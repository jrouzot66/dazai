<?php

namespace App\Repository;

use App\Entity\Delivery;
use App\Entity\Organization;

interface DeliveryRepositoryInterface
{
    /**
     * Retourne les livraisons liées à une organisation.
     *
     * @return Delivery[]
     */
    public function findForOrganization(Organization $org): array;
}
