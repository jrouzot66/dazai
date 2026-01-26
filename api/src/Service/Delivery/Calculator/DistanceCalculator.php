<?php

namespace App\Service\Delivery\Calculator;

use App\Entity\Delivery;

class DistanceCalculator extends AbstractDeliveryCalculator
{
    public function getName(): string
    {
        return 'distance';
    }

    public function supports(Delivery $delivery, string $transition, string $newStatus): bool
    {
        // V1: on calcule une "distance" quand on planifie
        return $transition === 'plan';
    }

    protected function doCalculate(Delivery $delivery, string $transition, string $newStatus): array
    {
        // Placeholder V1 : distance "factice" déterministe (à remplacer par PostGIS)
        $seed = strlen((string) $delivery->getPickupAddress()) + strlen((string) $delivery->getDropoffAddress());
        $distanceKm = max(1.0, round($seed / 10, 1));

        return [
            'distanceKm' => $distanceKm,
            'source' => 'v1_placeholder',
        ];
    }
}