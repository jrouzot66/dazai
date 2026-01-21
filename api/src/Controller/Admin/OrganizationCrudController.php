<?php
/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Controller\Admin;

use App\Entity\Organization;
use App\Entity\Enum\OrganizationType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

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

            ChoiceField::new('type', 'Type d\'organisation')
                ->setFormType(EnumType::class)
                ->setFormTypeOptions([
                    'class' => OrganizationType::class,
                    'choice_label' => fn (OrganizationType $choice) => $choice->getLabel(),
                ])
                ->formatValue(fn ($value, $entity) => $value instanceof OrganizationType ? $value->getLabel() : $value),

            AssociationField::new('whiteLabel', 'Marque Blanche associée'),
        ];
    }
}