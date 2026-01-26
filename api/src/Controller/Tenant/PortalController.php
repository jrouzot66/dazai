<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Controller\Tenant;

use App\Provider\MultiTenant\TenantConfigProvider;
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
    public function boot(TenantContextInterface $tenantContext, TenantConfigProvider $tenantConfig): Response
    {
        $tenant = $tenantContext->getCurrentTenant();

        if ($tenant === null) {
            throw $this->createNotFoundException('Tenant introuvable pour ce domaine.');
        }

        return $this->render('tenant/app_boot.html.twig', [
            'tenant' => $tenant,
            'config' => $tenantConfig->getConfig($tenant)
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

        $roles = $user->getRoles();

        $isMo = in_array('ROLE_MO_MANAGER', $roles, true);
        $isFo = in_array('ROLE_FO_VENDOR', $roles, true) || in_array('ROLE_FO_BUYER', $roles, true);

        // Priorité MO si les deux
        $portal = 'unknown';
        if ($isMo) {
            $portal = 'mo';
        } elseif ($isFo) {
            $portal = 'fo';
        }

        return new JsonResponse([
            'authenticated' => true,
            'email' => $user->getUserIdentifier(),
            'roles' => $roles,
            'portal' => $portal,
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