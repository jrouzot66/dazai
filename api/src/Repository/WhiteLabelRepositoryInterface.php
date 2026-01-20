<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Repository;

use App\Entity\WhiteLabel;

interface WhiteLabelRepositoryInterface
{
    public function findOneByDomainUrl(string $domainUrl): ?WhiteLabel;
}