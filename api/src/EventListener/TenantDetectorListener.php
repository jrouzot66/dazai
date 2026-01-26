<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\EventListener;

use App\Service\MultiTenant\TenantContextInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'kernel.request', method: 'onKernelRequest', priority: 100)]
class TenantDetectorListener
{
    public function __construct(
        private readonly TenantContextInterface $tenantContext
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $host = $request->getHost();

        if (in_array($host, TenantContextInterface::IGNORED_DOMAINS)) {
            return;
        }

        $tenant = $this->tenantContext->getCurrentTenant();

        if (!$tenant) {
            throw new NotFoundHttpException(TenantContextInterface::ERROR_NOT_FOUND);
        }

        // On utilise la clé centralisée pour stocker l'objet dans la requête
        $request->attributes->set(TenantContextInterface::ATTRIBUTE_KEY, $tenant);
    }
}