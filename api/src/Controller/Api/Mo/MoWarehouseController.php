<?php

namespace App\Controller\Api\Mo;

use App\Entity\Warehouse;
use App\Repository\WarehouseRepository;
use App\Service\MultiTenant\TenantContextInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/mo/warehouses', name: 'api_mo_warehouses_')]
class MoWarehouseController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_MO_MANAGER')]
    public function list(WarehouseRepository $repo): JsonResponse
    {
        $warehouses = $repo->findBy([], ['id' => 'DESC']);

        $data = array_map(static function (Warehouse $w): array {
            return [
                'id' => $w->getId(),
                'name' => $w->getName(),
                'address' => $w->getAddress(),
            ];
        }, $warehouses);

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

        $name = trim((string)($payload['name'] ?? ''));
        $address = trim((string)($payload['address'] ?? ''));

        if ($name === '' || $address === '') {
            return $this->json(['error' => 'Invalid payload'], 400);
        }

        $warehouse = (new Warehouse())
            ->setName($name)
            ->setAddress($address);

        $warehouse->setWhiteLabel($tenantContext->getCurrentTenant());

        $em->persist($warehouse);
        $em->flush();

        return $this->json([
            'id' => $warehouse->getId(),
            'name' => $warehouse->getName(),
            'address' => $warehouse->getAddress(),
        ], 201);
    }
}