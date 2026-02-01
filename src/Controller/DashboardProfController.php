<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/prof')]
class DashboardProfController extends AbstractController
{
    #[Route('', name: 'app_prof_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/prof/index.html.twig');
    }

    #[Route('/analyse', name: 'app_prof_analyse')]
    public function analyse(): Response
    {
        return $this->render('dashboard/prof/analyse.html.twig');
    }

    #[Route('/couts', name: 'app_prof_couts')]
    public function couts(): Response
    {
        return $this->render('dashboard/prof/couts.html.twig');
    }
}
