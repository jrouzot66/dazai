<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Service\MultiTenant;

use App\Entity\WhiteLabel;

interface TenantContextInterface
{
    public function getCurrentTenant(): ?WhiteLabel;
}