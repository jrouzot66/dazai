<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Repository;

use App\Entity\StaffUser;

interface StaffUserRepositoryInterface
{
    public function findByEmail(string $email): ?StaffUser;
}