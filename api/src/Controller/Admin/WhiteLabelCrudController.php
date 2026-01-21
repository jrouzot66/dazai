<?php
/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Controller\Admin;

use App\Entity\WhiteLabel;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;

class WhiteLabelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WhiteLabel::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom de la marque'),
            TextField::new('domainUrl', 'URL du Domaine'),

            // On utilise configString au lieu de config
            CodeEditorField::new('configString', 'Configuration JSON')
                ->setLanguage('js')
                ->setHelp('Format JSON attendu. Exemple: {"theme": "dark"}'),
        ];
    }
}