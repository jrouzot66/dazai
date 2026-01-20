<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Entity\Enum;

enum OrganizationType: string
{
    case VENDOR = 'vendor';
    case BUYER = 'buyer';
    case HYBRID = 'hybrid';
}