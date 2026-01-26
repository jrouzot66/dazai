<?php

namespace App\Service\Delivery\Computation;

use App\Entity\Delivery;
use App\Service\Delivery\Calculator\DeliveryCalculatorInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class DeliveryComputationEngine
{
    /**
     * @param iterable<DeliveryCalculatorInterface> $calculators
     */
    public function __construct(
        #[TaggedIterator('app.delivery_calculator')]
        private readonly iterable $calculators
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function computeAll(Delivery $delivery, string $transition, string $newStatus): array
    {
        $results = [];

        foreach ($this->calculators as $calculator) {
            $results[$calculator->getName()] = $calculator->calculate($delivery, $transition, $newStatus);
        }

        return $results;
    }
}