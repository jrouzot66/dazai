<?php

namespace App\Service\Delivery\Calculator;

use App\Entity\Delivery;

class EtaCalculator extends AbstractDeliveryCalculator
{
    public function getName(): string
    {
        return 'eta';
    }

    public function supports(Delivery $delivery, string $transition, string $newStatus): bool
    {
        return $transition === 'plan' && $delivery->getPlannedAt() !== null;
    }

    protected function doCalculate(Delivery $delivery, string $transition, string $newStatus): array
    {
        $plannedAt = $delivery->getPlannedAt();
        if ($plannedAt === null) {
            return ['ok' => false, 'reason' => 'plannedAt_missing'];
        }

        $eta = $plannedAt->modify('+2 hours');

        return [
            'etaAt' => $eta,
            'eta' => $eta->format(DATE_ATOM),
            'source' => 'v1_simple_rule',
        ];
    }
}