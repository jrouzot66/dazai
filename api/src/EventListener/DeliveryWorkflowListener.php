<?php

namespace App\EventListener;

use App\Entity\Delivery;
use App\Message\DeliveryStatusChangedMessage;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;

#[AsEventListener(event: 'workflow.delivery.completed.plan', method: 'onPlanned')]
#[AsEventListener(event: 'workflow.delivery.completed', method: 'onAnyCompleted')]
class DeliveryWorkflowListener
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ) {}

    public function onPlanned(CompletedEvent $event): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof Delivery) {
            return;
        }

        if ($subject->getPlannedAt() === null) {
            $subject->setPlannedAt(new \DateTimeImmutable());
        }
    }

    public function onAnyCompleted(CompletedEvent $event): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof Delivery) {
            return;
        }

        $deliveryId = $subject->getId();
        $tenantId = $subject->getWhiteLabel()?->getId();

        if ($deliveryId === null || $tenantId === null) {
            return;
        }

        $this->bus->dispatch(new DeliveryStatusChangedMessage(
            deliveryId: $deliveryId,
            whiteLabelId: $tenantId,
            newStatus: $subject->getStatus(),
            transition: $event->getTransition()->getName()
        ));
    }
}