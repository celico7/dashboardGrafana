<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $fullname = $request->request->get('fullname');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirm');
            $role = $request->request->get('role');

            // Validation basique
            $errors = [];

            if (!$fullname || strlen($fullname) < 3) {
                $errors[] = 'Le nom complet doit contenir au moins 3 caractères';
            }

            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email invalide';
            }

            if (!$password || strlen($password) < 8) {
                $errors[] = 'Le mot de passe doit contenir au moins 8 caractères';
            }

            if ($password !== $passwordConfirm) {
                $errors[] = 'Les mots de passe ne correspondent pas';
            }

            if (!in_array($role, ['ROLE_PROFESSEUR', 'ROLE_ETUDIANT', 'ROLE_GESTIONNAIRE'])) {
                $errors[] = 'Rôle invalide';
            }

            // Vérifier si l'email existe déjà
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $errors[] = 'Cet email est déjà utilisé';
            }

            if (empty($errors)) {
                // Créer le nouvel utilisateur
                $user = new User();
                $user->setFullname($fullname);
                $user->setEmail($email);
                $user->setRole($role);
                $user->setRoles([$role]);

                // Hasher le mot de passe
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                // Sauvegarder en BD
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_login');
            }

            return $this->render('auth/register.html.twig', [
                'errors' => $errors,
                'fullname' => $fullname,
                'email' => $email,
                'role' => $role,
            ]);
        }

        return $this->render('auth/register.html.twig', [
            'errors' => [],
            'fullname' => '',
            'email' => '',
            'role' => '',
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // This method can be blank - it will be intercepted by the logout key on your firewall
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
