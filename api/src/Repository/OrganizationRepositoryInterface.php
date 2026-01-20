<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Repository;

use App\Entity\Organization;
use App\Entity\WhiteLabel;

interface OrganizationRepositoryInterface
{
    /** @return Organization[] */
    public function findByWhiteLabel(WhiteLabel $whiteLabel): array;
}