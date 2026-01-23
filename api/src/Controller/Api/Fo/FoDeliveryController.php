<?php

namespace App\Controller\Api\Fo;

use App\Entity\AppUser;
use App\Entity\Delivery;
use App\Repository\DeliveryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Workflow\WorkflowInterface;

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

        $data = array_map(static function (Delivery $d): array {
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

    #[Route('/{id<\\d+>}/transitions/{transition}', name: 'transition', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function applyTransition(
        Delivery $delivery,
        string $transition,
        #[Autowire(service: 'state_machine.delivery')] WorkflowInterface $deliveryWorkflow,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user instanceof AppUser) {
            return $this->json(['authenticated' => false], 401);
        }

        $roles = $user->getRoles();
        $isVendor = in_array('ROLE_FO_VENDOR', $roles, true);

        // V1: seules les actions Vendor sont autorisées
        if (!$isVendor) {
            throw $this->createAccessDeniedException();
        }

        // V1: FO ne peut faire que start/deliver
        $allowedTransitions = ['start', 'deliver'];
        if (!in_array($transition, $allowedTransitions, true)) {
            return $this->json(['error' => 'Transition not allowed for FO', 'transition' => $transition], 403);
        }

        // Le vendor FO ne peut agir que sur ses deliveries
        $org = $user->getOrganization();
        if (!$org || !$delivery->getVendor() || $delivery->getVendor()->getId() !== $org->getId()) {
            throw $this->createAccessDeniedException();
        }

        if (!$deliveryWorkflow->can($delivery, $transition)) {
            return $this->json([
                'error' => 'Transition not allowed',
                'transition' => $transition,
                'status' => $delivery->getStatus(),
            ], 409);
        }

        $deliveryWorkflow->apply($delivery, $transition);
        $delivery->touch();
        $em->flush();

        return $this->json([
            'id' => $delivery->getId(),
            'status' => $delivery->getStatus(),
            'plannedAt' => $delivery->getPlannedAt()?->format(DATE_ATOM),
        ]);
    }
}