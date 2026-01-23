<?php

namespace App\Controller\Api\Fo;

use App\Entity\AppUser;
use App\Repository\DeliveryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/fo/deliveries', name: 'api_fo_deliveries_')]
class FoDeliveryController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(DeliveryRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof AppUser) {
            return $this->json(['authenticated' => false], 401);
        }

        $roles = $user->getRoles();
        $isFo = in_array('ROLE_FO_VENDOR', $roles, true) || in_array('ROLE_FO_BUYER', $roles, true);
        if (!$isFo) {
            throw $this->createAccessDeniedException();
        }

        $org = $user->getOrganization();
        if (!$org) {
            return $this->json(['error' => 'FO user must have an organization'], 400);
        }

        $deliveries = $repo->findForOrganization($org);

        $data = array_map(static function ($d): array {
            return [
                'id' => $d->getId(),
                'reference' => $d->getReference(),
                'status' => $d->getStatus(),
                'pickupAddress' => $d->getPickupAddress(),
                'dropoffAddress' => $d->getDropoffAddress(),
                'plannedAt' => $d->getPlannedAt()?->format(DATE_ATOM),
            ];
        }, $deliveries);

        return $this->json($data);
    }
}