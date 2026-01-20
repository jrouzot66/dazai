<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Service\MultiTenant;

use App\Entity\WhiteLabel;
use App\Repository\WhiteLabelRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class TenantContext implements TenantContextInterface
{
    private ?WhiteLabel $currentTenant = null;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly WhiteLabelRepository $whiteLabelRepository
    ) {}

    public function getCurrentTenant(): ?WhiteLabel
    {
        if ($this->currentTenant !== null) {
            return $this->currentTenant;
        }

        $request = $this->requestStack->getMainRequest();
        if ($request === null) {
            return null;
        }

        $host = $request->getHost();
        $this->currentTenant = $this->whiteLabelRepository->findOneBy(['domainUrl' => $host]);

        return $this->currentTenant;
    }
}