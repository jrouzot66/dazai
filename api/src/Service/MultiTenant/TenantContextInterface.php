<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Service\MultiTenant;

use App\Entity\WhiteLabel;

interface TenantContextInterface
{
    /**
     * Constantes centralisées pour le Multi-tenant
     */
    public const ATTRIBUTE_KEY = '_current_tenant';
    public const MASTER_DOMAIN = 'fluxion.local';
    public const IGNORED_DOMAINS = [
        self::MASTER_DOMAIN,
        'localhost',
        '127.0.0.1'
    ];
    public const ERROR_NOT_FOUND = "L'instance Fluxion demandée n'existe pas ou a été désactivée.";

    /**
     * Récupère la marque blanche identifiée pour la requête actuelle.
     */
    public function getCurrentTenant(): ?WhiteLabel;

    /**
     * Permet de forcer manuellement un tenant (utile pour les commandes CLI ou les tests).
     */
    public function setCurrentTenant(WhiteLabel $whiteLabel): void;
}