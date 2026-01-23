<?php

namespace App\EventListener;

use App\Entity\Delivery;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\Event\CompletedEvent;

#[AsEventListener(event: 'workflow.delivery.completed.plan', method: 'onPlanned')]
class DeliveryWorkflowListener
{
    public function onPlanned(CompletedEvent $event): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof Delivery) {
            return;
        }

        // Quand on planifie, si plannedAt n'est pas défini, on le pose.
        if ($subject->getPlannedAt() === null) {
            $subject->setPlannedAt(new \DateTimeImmutable());
        }
    }
}