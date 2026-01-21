<?php
/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Controller\Admin;

use App\Entity\Organization;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrganizationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Organization::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom de l\'organisation'),
            ChoiceField::new('type')
                ->setChoices([
                    'Fournisseur' => 'VENDOR',
                    'Acheteur' => 'BUYER',
                ]),
            AssociationField::new('whiteLabel', 'Marque Blanche associée'),
        ];
    }
}