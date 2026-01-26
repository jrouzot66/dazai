<?php

namespace App\Message;

class DeliveryStatusChangedMessage
{
    public function __construct(
        public readonly int $deliveryId,
        public readonly int $whiteLabelId,
        public readonly string $newStatus,
        public readonly string $transition
    ) {}
}