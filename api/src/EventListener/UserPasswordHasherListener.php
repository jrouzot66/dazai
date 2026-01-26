<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\EventListener;

use App\Entity\AppUser;
use App\Entity\StaffUser;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[AsEntityListener(event: Events::prePersist, method: 'hashPassword', entity: AppUser::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'hashPassword', entity: AppUser::class)]
#[AsEntityListener(event: Events::prePersist, method: 'hashPassword', entity: StaffUser::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'hashPassword', entity: StaffUser::class)]
class UserPasswordHasherListener
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * Doctrine passera directement l'entité (AppUser ou StaffUser) en argument.
     */
    public function hashPassword(PasswordAuthenticatedUserInterface $user): void
    {
        if (method_exists($user, 'getPlainPassword') && $user->getPlainPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPlainPassword()
            );
            $user->setPassword($hashedPassword);

            // On vide le mot de passe en clair par sécurité
            $user->setPlainPassword(null);
        }
    }
}