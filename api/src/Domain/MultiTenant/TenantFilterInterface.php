<?php
/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Domain\MultiTenant;

use Doctrine\ORM\Mapping\ClassMetadata;

interface TenantFilterInterface
{
    /**
     * Nom du paramètre utilisé par Doctrine pour stocker l'ID du tenant.
     */
    public const PARAMETER_NAME = 'white_label_id';

    /**
     * Identifiant unique du filtre dans la configuration Symfony.
     */
    public const FILTER_ID = 'tenant_filter';

    /**
     * Signature de la méthode de filtrage de Doctrine.
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string;
}