<?php


namespace App\Service\Delivery\Calculator;

use App\Entity\Delivery;

interface DeliveryCalculatorInterface
{
    public function getName(): string;

    public function supports(Delivery $delivery, string $transition, string $newStatus): bool;

    /**
     * @return array<string, mixed>
     */
    public function calculate(Delivery $delivery, string $transition, string $newStatus): array;
}