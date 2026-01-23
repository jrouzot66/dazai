<?php

namespace App\Controller\Api\Mo;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/mo', name: 'api_mo_')]
class MoHealthController extends AbstractController
{
    #[Route('/ping', name: 'ping', methods: ['GET'])]
    #[IsGranted('ROLE_MO_MANAGER')]
    public function ping(): JsonResponse
    {
        return $this->json(['ok' => true, 'scope' => 'mo']);
    }
}