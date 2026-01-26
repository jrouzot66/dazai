<?php

namespace App\Controller\Api;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/organizations', name: 'api_organizations_')]
class OrganizationController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_MO_MANAGER')]
    public function list(OrganizationRepository $repo): JsonResponse
    {
        $organizations = $repo->findAll();

        $data = array_map(static function (Organization $org): array {
            return [
                'id' => $org->getId(),
                'name' => $org->getName(),
            ];
        }, $organizations);

        return $this->json($data);
    }
}