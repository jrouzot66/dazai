<?php

namespace App\Controller\Api\Mo;

use App\Entity\Delivery;
use App\Entity\Organization;
use App\Repository\DeliveryRepository;
use App\Service\MultiTenant\TenantContextInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/api/mo/deliveries', name: 'api_mo_deliveries_')]
class MoDeliveryController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_MO_MANAGER')]
    public function list(DeliveryRepository $repo): JsonResponse
    {
        $deliveries = $repo->findBy([], ['id' => 'DESC']);

        $data = array_map(static function (Delivery $d): array {
            return [
                'id' => $d->getId(),
                'reference' => $d->getReference(),
                'status' => $d->getStatus(),
                'pickupAddress' => $d->getPickupAddress(),
                'dropoffAddress' => $d->getDropoffAddress(),
                'plannedAt' => $d->getPlannedAt()?->format(DATE_ATOM),
                'vendor' => $d->getVendor()?->getName(),
                'buyer' => $d->getBuyer()?->getName(),
            ];
        }, $deliveries);

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[IsGranted('ROLE_MO_MANAGER')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        TenantContextInterface $tenantContext
    ): JsonResponse {
        $payload = json_decode($request->getContent() ?: '{}', true);

        $pickupAddress = (string)($payload['pickupAddress'] ?? '');
        $dropoffAddress = (string)($payload['dropoffAddress'] ?? '');
        $vendorId = (int)($payload['vendorId'] ?? 0);
        $buyerId = (int)($payload['buyerId'] ?? 0);

        if ($pickupAddress === '' || $dropoffAddress === '' || $vendorId <= 0 || $buyerId <= 0) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }

        $vendor = $em->getRepository(Organization::class)->find($vendorId);
        $buyer = $em->getRepository(Organization::class)->find($buyerId);

        if (!$vendor || !$buyer) {
            return $this->json(['error' => 'Organization not found'], 404);
        }

        $delivery = (new Delivery())
            ->setPickupAddress($pickupAddress)
            ->setDropoffAddress($dropoffAddress)
            ->setVendor($vendor)
            ->setBuyer($buyer);

        $delivery->setWhiteLabel($tenantContext->getCurrentTenant());

        $em->persist($delivery);
        $em->flush();

        return $this->json([
            'id' => $delivery->getId(),
            'reference' => $delivery->getReference(),
            'status' => $delivery->getStatus(),
            'pickupAddress' => $delivery->getPickupAddress(),
            'dropoffAddress' => $delivery->getDropoffAddress(),
            'plannedAt' => $delivery->getPlannedAt()?->format(DATE_ATOM),
            'vendor' => $delivery->getVendor()?->getName(),
            'buyer' => $delivery->getBuyer()?->getName(),
        ], 201);
    }

    #[Route('/{id<\\d+>}/transitions/{transition}', name: 'transition', methods: ['POST'])]
    #[IsGranted('ROLE_MO_MANAGER')]
    public function applyTransition(
        Delivery $delivery,
        string $transition,
        #[Autowire(service: 'workflow.delivery')] WorkflowInterface $deliveryWorkflow,
        EntityManagerInterface $em
    ): JsonResponse {
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
        ]);
    }
}