<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Repository;

use App\Entity\AppUser;

interface AppUserRepositoryInterface
{
    public function save(AppUser $user, bool $flush = false): void;
    public function remove(AppUser $user, bool $flush = false): void;
    public function findByEmail(string $email): ?AppUser;
}