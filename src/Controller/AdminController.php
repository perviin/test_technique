<?php

namespace App\Controller;

use App\Service\CSVImportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    private CSVImportService $csvImportService;

    public function __construct(CSVImportService $csvImportService)
    {
        $this->csvImportService = $csvImportService;
    }

    #[Route('/import-users', name: 'app_admin_import_users', methods: ['GET', 'POST'])]
    public function importUsers(Request $request): Response
    {
        if ($request->isMethod('POST') && $request->files->has('csv_file')) {
            /** @var UploadedFile $file */
            $file = $request->files->get('csv_file');

            // Appel du service pour importer le fichier CSV
            $importData = $this->csvImportService->importCSV($file);

            return $this->render('admin/import_users.html.twig', [
                'usersImported' => $importData['usersImported'],
                'validationErrors' => $importData['validationErrors']
            ]);
        }

        return $this->render('admin/import_users.html.twig');
    }
}

