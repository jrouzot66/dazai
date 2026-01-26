<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Controller\Admin;

use App\Entity\AppUser;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AppUserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AppUser::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email', 'Email utilisateur'),

            // Le mot de passe n'est affiché qu'à la création
            TextField::new('plainPassword', 'Nouveau mot de passe')
                ->setFormType(PasswordType::class)
                ->onlyOnForms()
                ->setRequired($pageName === 'new'),

            ChoiceField::new('roles', 'Rôles Applicatifs')
                ->allowMultipleChoices()
                ->setChoices([
                    'Middle Office (Manager)' => 'ROLE_MO_MANAGER',
                    'Front Office (Vendor/Chauffeur)' => 'ROLE_FO_VENDOR',
                    'Front Office (Buyer/Réception)' => 'ROLE_FO_BUYER',
                ]),

            AssociationField::new('whiteLabel', 'Marque Blanche associée'),

            AssociationField::new('organization', 'Organisation (Optionnel pour MO)')
                ->setHelp('Laissez vide pour les gestionnaires de la marque blanche.'),
        ];
    }
}