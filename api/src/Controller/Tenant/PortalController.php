<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Controller\Tenant;

use App\Service\MultiTenant\TenantContextInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortalController extends AbstractController
{
    /**
     * Cette route capture la racine ET toutes les routes qui ne commencent pas par /api ou /admin
     */
    #[Route('/{vueRouting}', name: 'app_entrypoint', requirements: ['vueRouting' => '^(?!api|admin).*'], defaults: ['vueRouting' => null])]
    public function boot(TenantContextInterface $tenantContext): Response
    {
        $tenant = $tenantContext->getCurrentTenant();

        return $this->render('tenant/app_boot.html.twig', [
            'tenant' => $tenant,
            'config' => $tenant->getConfig()
        ]);
    }

    /**
     * Route appelée par VueJS pour vérifier si l'utilisateur est connecté.
     */
    #[Route('/api/logged', name: 'api_logged', methods: ['GET'])]
    public function logged(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['authenticated' => false], 401);
        }

        return new JsonResponse([
            'authenticated' => true,
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // On renvoie un JSON vide au cas où le firewall laisserait passer (pour éviter la 500)
        return new JsonResponse(['error' => 'Security firewall not active'], 400);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return new JsonResponse(null, 204);
    }
}