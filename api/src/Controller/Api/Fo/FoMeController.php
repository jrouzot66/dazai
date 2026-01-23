<?php

namespace App\Controller\Api\Fo;

use App\Entity\AppUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/fo', name: 'api_fo_')]
class FoMeController extends AbstractController
{
    #[Route('/me', name: 'me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        /** @var AppUser|null $user */
        $user = $this->getUser();
        if (!$user instanceof AppUser) {
            return $this->json(['authenticated' => false], 401);
        }

        $roles = $user->getRoles();
        $isFo = in_array('ROLE_FO_VENDOR', $roles, true) || in_array('ROLE_FO_BUYER', $roles, true);

        if (!$isFo) {
            throw $this->createAccessDeniedException();
        }

        return $this->json([
            'authenticated' => true,
            'portal' => 'fo',
            'email' => $user->getUserIdentifier(),
            'roles' => $roles,
        ]);
    }
}