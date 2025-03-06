<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Infos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CSVImportService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
    }

    public function importCSV(UploadedFile $file): array
    {
        $usersImported = [];
        $validationErrors = [];

        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            fgetcsv($handle, 1000, ';');

            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                if (count($data) < 4) {
                    continue;
                }

                $username = $data[0] ?? null;
                $email = $data[1] ?? null;
                $password = $data[2] ?? null;
                $role = $data[3] ?? 'ROLE_USER';
                $rank = $data[4] ?? 'Débutant';
                $victoire = is_numeric($data[5] ?? null) ? $data[5] : 0;
                $defaite = is_numeric($data[6] ?? null) ? $data[6] : 0;

                if (!$username || !$email || !$password) {
                    continue;
                }

                $user = new User();
                $user->setUsername($username);
                $user->setEmail($email);
                $user->setPassword($this->passwordHasher->hashPassword($user, $password));
                $user->setRoles([$role]);

                $errors = $this->validator->validate($user);
                if (count($errors) === 0) {
                    $this->entityManager->persist($user);

                    $infos = new Infos();
                    $infos->setUser($user);
                    $infos->setUserRank($rank);
                    $infos->setVictoire($victoire);
                    $infos->setDefaite($defaite);
                    $this->entityManager->persist($infos);

                    $usersImported[] = $user;
                } else {
                    $validationErrors[] = [
                        'user' => $username,
                        'errors' => $this->formatValidationErrors($errors)
                    ];
                }
            }
            fclose($handle);
            $this->entityManager->flush();
        }

        return [
            'usersImported' => $usersImported,
            'validationErrors' => $validationErrors
        ];
    }


    private function formatValidationErrors($errors): array
    {
        $formattedErrors = [];
        foreach ($errors as $error) {
            $formattedErrors[] = $error->getPropertyPath() . ': ' . $error->getMessage();
        }
        return $formattedErrors;
    }
}
