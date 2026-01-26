<?php

namespace App\Entity\Enum;

enum DeliveryStatus: string
{
    case DRAFT = 'draft';
    case PLANNED = 'planned';
    case IN_TRANSIT = 'in_transit';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}