<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Service\MultiTenant;

use App\Entity\WhiteLabel;
use App\Repository\WhiteLabelRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TenantContext implements TenantContextInterface
{
    private ?WhiteLabel $currentTenant = null;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly WhiteLabelRepositoryInterface $whiteLabelRepository
    ) {}

    public function getCurrentTenant(): ?WhiteLabel
    {
        if ($this->currentTenant !== null) {
            return $this->currentTenant;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        if ($request->attributes->has(self::ATTRIBUTE_KEY)) {
            $this->currentTenant = $request->attributes->get(self::ATTRIBUTE_KEY);
            return $this->currentTenant;
        }

        $host = $request->getHost();
        $this->currentTenant = $this->whiteLabelRepository->findOneByDomainUrl($host);

        return $this->currentTenant;
    }

    public function setCurrentTenant(WhiteLabel $whiteLabel): void
    {
        $this->currentTenant = $whiteLabel;

        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $request->attributes->set(self::ATTRIBUTE_KEY, $whiteLabel);
        }
    }
}