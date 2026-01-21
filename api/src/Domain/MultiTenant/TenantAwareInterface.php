<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Domain\MultiTenant;

use App\Entity\WhiteLabel;

interface TenantAwareInterface
{
    public function getWhiteLabel(): ?WhiteLabel;
    public function setWhiteLabel(?WhiteLabel $whiteLabel): self;
}