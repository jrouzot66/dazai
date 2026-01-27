<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class RootRedirectController
{
    #[Route(
        '/',
        name: 'root_redirect_fluxion_local',
        methods: ['GET'],
        host: 'fluxion.local',
        priority: 10
    )]
    public function __invoke(): RedirectResponse
    {
        return new RedirectResponse('/admin/login', 302);
    }
}