<?php


namespace App\Service\Delivery\Calculator;

use App\Entity\Delivery;

abstract class AbstractDeliveryCalculator implements DeliveryCalculatorInterface
{
    public function calculate(Delivery $delivery, string $transition, string $newStatus): array
    {
        if (!$this->supports($delivery, $transition, $newStatus)) {
            return [
                'ok' => false,
                'skipped' => true,
                'name' => $this->getName(),
            ];
        }

        $result = $this->doCalculate($delivery, $transition, $newStatus);

        return array_merge(
            ['ok' => true, 'name' => $this->getName()],
            $result
        );
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function doCalculate(Delivery $delivery, string $transition, string $newStatus): array;
}