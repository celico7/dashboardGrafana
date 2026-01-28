<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/dashboard', name: 'app_dashboard')]
    public function redirectByRole(): Response
    {
        // Redirection par défaut vers le dashboard élève (pas de sécurité)
        return $this->redirectToRoute('app_eleve_dashboard');
    }

    #[Route('/prof', name: 'app_prof_dashboard')]
    public function prof(): Response
    {
        return $this->render('dashboard/prof.html.twig', [
            'grafana_url' => $_ENV['GRAFANA_URI'] ?? 'http://localhost:3000'
        ]);
    }

    #[Route('/eleve', name: 'app_eleve_dashboard')]
    public function eleve(): Response
    {
        return $this->render('dashboard/eleve.html.twig', [
            'grafana_url' => $_ENV['GRAFANA_URI'] ?? 'http://localhost:3000'
        ]);
    }

    #[Route('/admin', name: 'app_admin_dashboard')]
    public function admin(): Response
    {
        return $this->render('dashboard/admin.html.twig', [
            'grafana_url' => $_ENV['GRAFANA_URI'] ?? 'http://localhost:3000'
        ]);
    }
}