<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\EventListener;

use App\Domain\MultiTenant\TenantFilterInterface;
use App\Service\MultiTenant\TenantContextInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'kernel.request', method: 'onKernelRequest', priority: 5)]
class TenantFilterConfiguratorListener
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TenantContextInterface $tenantContext
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        // On ne traite que la requête principale
        if (!$event->isMainRequest()) {
            return;
        }

        $tenant = $this->tenantContext->getCurrentTenant();

        // Si on a détecté un tenant (on est sur une marque blanche)
        if ($tenant) {
            // On active le filtre Doctrine
            $filter = $this->entityManager->getFilters()->enable(TenantFilterInterface::FILTER_ID);

            // On lui passe l'ID pour le WHERE SQL
            $filter->setParameter(TenantFilterInterface::PARAMETER_NAME, $tenant->getId());
        }
    }
}