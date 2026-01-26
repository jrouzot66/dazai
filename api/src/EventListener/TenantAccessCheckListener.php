<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\EventListener;

use App\Entity\AppUser;
use App\Service\MultiTenant\TenantContextInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class TenantAccessCheckListener
{
    public function __construct(private readonly TenantContextInterface $tenantContext) {}

    #[AsEventListener(event: CheckPassportEvent::class)]
    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $user = $event->getPassport()->getUser();
        if (!$user instanceof AppUser) return;

        $currentTenant = $this->tenantContext->getCurrentTenant();

        // C'est ici que la magie opère :
        // Si l'utilisateur appartient à la WhiteLabel ID 1 mais tente
        // de se loguer sur le domaine lié à l'ID 2, on le jette.
        if ($user->getWhiteLabel() !== $currentTenant) {
            throw new CustomUserMessageAuthenticationException(
                "Accès refusé : votre compte n'appartient pas à cette instance Fluxion."
            );
        }
    }
}