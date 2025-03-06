<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Infos;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile_show');
        }

        return $this->redirectToRoute('app_register');
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectUserAfterLogin();
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($this->getUser()) {
            return $this->redirectUserAfterLogin();
        }

        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);

            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            if (stripos($username, 'admin') !== false) {
                $user->setRoles(['ROLE_ADMIN']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }

            $infos = new Infos();
            $infos->setUserRank('Débutant');
            $infos->setVictoire(0);
            $infos->setDefaite(0);
            $user->setInfos($infos);

            $entityManager->persist($user);
            $entityManager->persist($infos);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/register.html.twig');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    private function redirectUserAfterLogin(): Response
    {
        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('app_user_index');
        }

        return $this->redirectToRoute('app_profile_show');
    }
}