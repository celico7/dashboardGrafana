<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function redirectByRole(): Response
    {
        if ($this->isGranted('ROLE_PROFESSEUR')) {
            return $this->redirectToRoute('app_prof_dashboard');
        }

        if ($this->isGranted('ROLE_ETUDIANT')) {
            return $this->redirectToRoute('app_eleve_dashboard');
        }

        if ($this->isGranted('ROLE_GESTIONNAIRE')) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->redirectToRoute('app_login');
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