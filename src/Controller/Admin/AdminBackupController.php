<?php

namespace App\Controller\Admin;

use App\Entity\Backup;
use App\Service\BackupService;
use App\Repository\BackupRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/backup')]
class AdminBackupController extends AbstractController
{
    public function __construct(
        private BackupService $backupService,
        private BackupRepository $backupRepository
    ) {
    }

    #[Route('/', name: 'app_backup_admin_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/backup/index.html.twig', [
            'backups' => $this->backupRepository->findAll(),
        ]);
    }

    #[Route('/show/{id}', name: 'app_backup_admin_show', methods: ['GET'])]
    public function show(Backup $backup): Response
    {
        return $this->render('admin/backup/show.html.twig', [
            'backup' => $backup,
        ]);
    }

    #[Route('/create', name: 'app_backup_admin_create')]
    public function create(): Response
    {
        $filename = $this->backupService->save()->getFilename();
        
        if (null === $filename) {
            $this->addFlash('error', "Il y a eu une erreur et la sauvegarde a échoué.");
        } else {
            $backup = (new Backup())
                ->setFilename($filename)
                ->setCreatedAt($this->backupService->getDate())
            ;
            $this->addFlash('success', "La base de donnée a été enregistrée.");
            $this->backupRepository->save($backup, true);
        }

        return $this->redirectToRoute('app_backup_admin_index');
    }

    #[Route('/download/{id}', name: 'app_backup_admin_download')]
    public function download(Backup $backup): BinaryFileResponse
    {
        $folder = $this->backupService->getBackupFolder();
        $file = $folder.DIRECTORY_SEPARATOR.$backup->getFilename();

        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $backup->getFilename()
        );

        return $response;
    }

    #[Route('/{id}', name: 'app_admin_backup_delete', methods: ['POST'])]
    public function delete(Backup $backup): Response
    {
        $path = $this->backupService->getBackupFolder() . DIRECTORY_SEPARATOR . $backup->getFilename();

        if (file_exists($path)) {
            unlink($path);
        }

        $this->backupRepository->remove($backup, true);
        $this->addFlash('success', "La sauvegarde a été supprimée.");

        return $this->redirectToRoute('app_backup_admin_index');
    }
}
