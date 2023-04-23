<?php

namespace App\Controller\Admin;

use App\Entity\Settings;
use App\Form\SettingsType;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', ['activeDashboard' => true]);
    }

    #[Route('/admin/settings', name: 'app_admin_settings')]
    public function settings(Request $request, SettingsRepository $settingsRepository): Response
    {
        $settings = $settingsRepository->findSettings();

        if (null === $settings) {
            $settings = new Settings();
        }

        $form = $this->createForm(SettingsType::class, $settings);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $settingsRepository->save($settings, true);

            return $this->redirectToRoute('app_admin_settings');
        }

        return $this->render('admin/settings.html.twig', [
            'activeSettings' => true,
            'form' => $form->createView(),
        ]);
    }
}
