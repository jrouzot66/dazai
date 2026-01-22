<?php
/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Controller\Admin;

use App\Entity\AppUser;
use App\Entity\WhiteLabel;
use App\Entity\Organization;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Dazai - Administration Globale')
            ->renderContentMaximized(true);
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        yield MenuItem::section('Gestion Multi-tenant');
        yield MenuItem::linkToCrud('Marques Blanches', 'fa-solid fa-tags', WhiteLabel::class);
        yield MenuItem::linkToCrud('Organisations', 'fa-solid fa-building', Organization::class);
        yield MenuItem::linkToCrud('Utilisateurs Clients', 'fa fa-users', AppUser::class);

        yield MenuItem::section('Sécurité');
        yield MenuItem::linkToLogout('Déconnexion', 'fa-solid fa-right-from-bracket');
    }
}