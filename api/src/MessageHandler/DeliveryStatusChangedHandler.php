<?php

namespace App\MessageHandler;

use App\Entity\Delivery;
use App\Message\DeliveryStatusChangedMessage;
use App\Repository\DeliveryRepository;
use App\Service\Delivery\Computation\DeliveryComputationEngine;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeliveryStatusChangedHandler
{
    public function __construct(
        private readonly DeliveryRepository $deliveries,
        private readonly DeliveryComputationEngine $computer,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(DeliveryStatusChangedMessage $message): void
    {
        /** @var Delivery|null $delivery */
        $delivery = $this->deliveries->findOneBy([
            'id' => $message->deliveryId,
            'whiteLabel' => $message->whiteLabelId,
        ]);

        if (!$delivery) {
            $this->logger->warning('Delivery not found (async)', [
                'deliveryId' => $message->deliveryId,
                'whiteLabelId' => $message->whiteLabelId,
            ]);

            return;
        }

        $results = $this->computer->computeAll($delivery, $message->transition, $message->newStatus);

        // Persist ETA
        $eta = $results['eta'] ?? null;
        if (is_array($eta) && ($eta['ok'] ?? false) === true && isset($eta['etaAt']) && $eta['etaAt'] instanceof \DateTimeImmutable) {
            $delivery->setEtaAt($eta['etaAt']);
        }

        // Persist distance
        $distance = $results['distance'] ?? null;
        if (is_array($distance) && ($distance['ok'] ?? false) === true && isset($distance['distanceKm'])) {
            $distanceKm = $distance['distanceKm'];
            if (is_float($distanceKm) || is_int($distanceKm)) {
                $delivery->setDistanceKm((float) $distanceKm);
            }
        }

        $delivery->touch();
        $this->em->flush();

        $this->logger->info('Delivery computed & persisted (async)', [
            'deliveryId' => $delivery->getId(),
            'transition' => $message->transition,
            'newStatus' => $message->newStatus,
            'etaAt' => $delivery->getEtaAt()?->format(DATE_ATOM),
            'distanceKm' => $delivery->getDistanceKm(),
        ]);
    }
}