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

    public function getLabel(): string
    {
        return match($this) {
            self::VENDOR => 'Fournisseur',
            self::BUYER => 'Acheteur',
            self::HYBRID => 'Hybride (Fournisseur & Acheteur)',
        };
    }
}