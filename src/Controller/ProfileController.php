<?php

namespace App\Controller;

use App\Entity\Infos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile_show')]
    public function show(EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Récupérer les infos liées à cet utilisateur (s'il existe)
        $infos = $entityManager->getRepository(Infos::class)->findOneBy(['user' => $user]);

        // Si aucune info n'existe, en créer une
        if (!$infos) {
            $infos = new Infos();
            $infos->setUser($user);
            $infos->setUserRank('Débutant'); // Valeur par défaut
            $infos->setVictoire('0');
            $infos->setDefaite('0');

            $entityManager->persist($infos);
            $entityManager->flush();
        }

        // Récupérer le user_rank
        $userRank = $infos->getUserRank();

        // Passer les informations au template
        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'userRank' => $userRank,  // Passer le user_rank au template
        ]);
    }
}
