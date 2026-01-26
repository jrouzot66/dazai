<?php

namespace App\Tests\Service\Delivery\Calculator;

use App\Entity\Delivery;
use App\Service\Delivery\Calculator\DistanceCalculator;
use PHPUnit\Framework\TestCase;

class DistanceCalculatorTest extends TestCase
{
    public function testSupportsOnlyOnPlanTransition(): void
    {
        $delivery = new Delivery();
        $delivery->setPickupAddress('A');
        $delivery->setDropoffAddress('B');

        $calculator = new DistanceCalculator();

        self::assertTrue($calculator->supports($delivery, 'plan', 'planned'));
        self::assertFalse($calculator->supports($delivery, 'start', 'in_transit'));
        self::assertFalse($calculator->supports($delivery, 'deliver', 'delivered'));
    }

    public function testCalculateReturnsDistanceKmWhenSupported(): void
    {
        $delivery = new Delivery();
        $delivery->setPickupAddress('10 rue de Paris');
        $delivery->setDropoffAddress('20 avenue de Lyon');

        $calculator = new DistanceCalculator();

        $result = $calculator->calculate($delivery, 'plan', 'planned');

        self::assertIsArray($result);
        self::assertTrue($result['ok']);
        self::assertSame('distance', $result['name']);
        self::assertArrayHasKey('distanceKm', $result);
        self::assertIsFloat($result['distanceKm']);
        self::assertGreaterThan(0.0, $result['distanceKm']);
        self::assertSame('v1_placeholder', $result['source']);
    }

    public function testCalculateIsSkippedWhenNotSupported(): void
    {
        $delivery = new Delivery();
        $delivery->setPickupAddress('A');
        $delivery->setDropoffAddress('B');

        $calculator = new DistanceCalculator();

        $result = $calculator->calculate($delivery, 'start', 'in_transit');

        self::assertIsArray($result);
        self::assertFalse($result['ok']);
        self::assertTrue($result['skipped']);
        self::assertSame('distance', $result['name']);
    }
}