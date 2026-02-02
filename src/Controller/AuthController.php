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
        $error = $authenticationUtils->getLastAuthenticationError();

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
                $errors['fullname'] = ['Le nom complet doit contenir au moins 3 caractères'];
            }

            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = ['Email invalide'];
            }

            // Validation du mot de passe sécurisé
            $passwordErrors = [];
            if (!$password || strlen($password) < 12) {
                $passwordErrors[] = 'Le mot de passe doit contenir au moins 12 caractères';
            }
            if ($password && !preg_match('/[0-9]/', $password)) {
                $passwordErrors[] = 'Le mot de passe doit contenir au moins 1 chiffre';
            }
            if ($password && !preg_match('/[!@#$%^&*(),.?":{}|<>_\-+=\[\]\/;~`]/', $password)) {
                $passwordErrors[] = 'Le mot de passe doit contenir au moins 1 caractère spécial';
            }

            if (!empty($passwordErrors)) {
                $errors['password'] = $passwordErrors;
            }

            if ($password !== $passwordConfirm) {
                $errors['password_confirm'] = ['Les mots de passe ne correspondent pas'];
            }

            if (!in_array($role, ['ROLE_PROFESSEUR', 'ROLE_ETUDIANT', 'ROLE_GESTIONNAIRE'])) {
                $errors['role'] = ['Rôle invalide'];
            }

            // Vérifier si l'email existe déjà
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $errors['email'] = ['Cet email est déjà utilisé'];
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
        throw new \LogicException('Cette méthode peut rester vide - elle sera interceptée par la clé logout de votre firewall.');
    }

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $errors = [];

            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = ['Email invalide'];
            }

            if (empty($errors)) {
                $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $user->setResetToken($token);
                    $user->setResetTokenExpiry(new \DateTime('+1 hour'));
                    $entityManager->flush();

                    return $this->redirectToRoute('app_reset_password', ['token' => $token]);
                } else {
                    $errors['email'] = ['Aucun compte associé à cet email'];
                }
            }

            return $this->render('auth/forgot_password.html.twig', [
                'errors' => $errors,
                'email' => $email,
            ]);
        }

        return $this->render('auth/forgot_password.html.twig', [
            'errors' => [],
            'email' => '',
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(
        string $token,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user || !$user->getResetTokenExpiry() || $user->getResetTokenExpiry() < new \DateTime()) {
            $this->addFlash('error', 'Le lien de réinitialisation est invalide ou expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirm');
            $errors = [];

            $passwordErrors = [];
            if (!$password || strlen($password) < 12) {
                $passwordErrors[] = 'Le mot de passe doit contenir au moins 12 caractères';
            }
            if ($password && !preg_match('/[0-9]/', $password)) {
                $passwordErrors[] = 'Le mot de passe doit contenir au moins 1 chiffre';
            }
            if ($password && !preg_match('/[!@#$%^&*(),.?":{}|<>_\-+=\[\]\/;~`]/', $password)) {
                $passwordErrors[] = 'Le mot de passe doit contenir au moins 1 caractère spécial';
            }

            if (!empty($passwordErrors)) {
                $errors['password'] = $passwordErrors;
            }

            if ($password !== $passwordConfirm) {
                $errors['password_confirm'] = ['Les mots de passe ne correspondent pas'];
            }

            if (empty($errors)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
                $user->setResetToken(null);
                $user->setResetTokenExpiry(null);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe réinitialisé avec succès.');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('auth/reset_password.html.twig', [
                'errors' => $errors,
                'token' => $token,
            ]);
        }

        return $this->render('auth/reset_password.html.twig', [
            'errors' => [],
            'token' => $token,
        ]);
    }
}

