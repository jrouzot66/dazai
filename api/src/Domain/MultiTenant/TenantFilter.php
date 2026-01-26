<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Domain\MultiTenant;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class TenantFilter extends SQLFilter
{
    public const PARAMETER_NAME = 'white_label_id';

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        // On vérifie si l'entité implémente notre interface
        if (!$targetEntity->reflClass->implementsInterface(TenantAwareInterface::class)) {
            return '';
        }

        try {
            $tenantId = $this->getParameter(self::PARAMETER_NAME);
        } catch (\InvalidArgumentException) {
            return ''; // Pas de tenant défini = pas de filtre (ex: BO Dazai)
        }

        // On injecte : WHERE table_alias.white_label_id = :id
        return sprintf('%s.white_label_id = %s', $targetTableAlias, $tenantId);
    }
}