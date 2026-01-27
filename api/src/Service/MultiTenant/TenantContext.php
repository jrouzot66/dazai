<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Service\MultiTenant;

use App\Entity\WhiteLabel;
use App\Repository\WhiteLabelRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TenantContext implements TenantContextInterface
{
    private ?WhiteLabel $currentTenant = null;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly WhiteLabelRepositoryInterface $whiteLabelRepository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function getCurrentTenant(): ?WhiteLabel
    {
        // Si on a déjà un tenant en cache, on renvoie une référence MANAGED
        if ($this->currentTenant !== null) {
            $id = $this->currentTenant->getId();
            return $id ? $this->em->getReference(WhiteLabel::class, $id) : $this->currentTenant;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        if ($request->attributes->has(self::ATTRIBUTE_KEY)) {
            $tenant = $request->attributes->get(self::ATTRIBUTE_KEY);
            if ($tenant instanceof WhiteLabel) {
                $this->currentTenant = $tenant;

                $id = $tenant->getId();
                return $id ? $this->em->getReference(WhiteLabel::class, $id) : $tenant;
            }
        }

        $host = $request->getHost();
        $this->currentTenant = $this->whiteLabelRepository->findOneByDomainUrl($host);

        if ($this->currentTenant === null) {
            return null;
        }

        $id = $this->currentTenant->getId();
        return $id ? $this->em->getReference(WhiteLabel::class, $id) : $this->currentTenant;
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