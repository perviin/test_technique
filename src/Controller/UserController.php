<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Infos;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\CSVImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private CSVImportService $csvImportService;

    public function __construct(UserPasswordHasherInterface $passwordHasher, CSVImportService $csvImportService)
    {
        $this->passwordHasher = $passwordHasher;
        $this->csvImportService = $csvImportService;
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user_crud.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST') && $request->files->has('csv_file')) {
            $file = $request->files->get('csv_file');

            // Appel du service pour importer le fichier CSV
            $importData = $this->csvImportService->importCSV($file);

            return $this->render('admin/import_users.html.twig', [
                'usersImported' => $importData['usersImported'],
                'validationErrors' => $importData['validationErrors']
            ]);
        }

        return $this->render('admin/import_users.html.twig', [
            'usersImported' => [],
            'validationErrors' => []
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();

            if (!empty($newPassword)) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('admin/user_edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index');
    }
}
