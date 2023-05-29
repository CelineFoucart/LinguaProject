<?php

namespace App\Controller\Admin;

use App\Entity\Settings;
use App\Form\SettingsType;
use App\Service\ConfigService;
use App\Form\AboutSettingsType;
use App\Form\IndexSettingsType;
use App\Form\AdvancedSettingsType;
use App\Repository\SettingsRepository;
use App\Service\Statistics\SatisticsEntity;
use App\Service\Statistics\StatisticsHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(StatisticsHandler $statisticsHandler): Response
    {
        $stats = $statisticsHandler
            ->addEntity(new SatisticsEntity('article'))
            ->addEntity(new SatisticsEntity('category'))
            ->addEntity(new SatisticsEntity('document'))
            ->getStatistics()
        ;
        
        return $this->render('admin/dashboard.html.twig', ['activeDashboard' => true, 'stats' => $stats]);
    }

    #[Route('/admin/settings/index', name: 'app_admin_settings_index')]
    public function settings(Request $request, SettingsRepository $settingsRepository): Response
    {
        $settings = $settingsRepository->findSettings();

        if (null === $settings) {
            $settings = new Settings();
        }

        $form = $this->createForm(IndexSettingsType::class, $settings);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $settingsRepository->save($settings, true);

            return $this->redirectToRoute('app_admin_settings_index');
        }

        return $this->render('admin/index_settings.html.twig', [
            'activeIndexSettings' => true,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/settings/about', name: 'app_admin_settings_about')]
    public function settingsAbout(Request $request, SettingsRepository $settingsRepository): Response
    {
        $settings = $settingsRepository->findSettings();

        if (null === $settings) {
            $settings = new Settings();
        }

        $form = $this->createForm(AboutSettingsType::class, $settings);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $settingsRepository->save($settings, true);

            return $this->redirectToRoute('app_admin_settings_about');
        }

        return $this->render('admin/about_settings.html.twig', [
            'activeAboutSettings' => true,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/settings/advanced', name: 'app_admin_settings_advanced')]
    public function action(Request $request, ConfigService $configService): Response
    {
        $envVars = $configService->getEnvVars();
        $hasEnvFile = $configService->hasConfigFile();

        $form = $this->createForm(AdvancedSettingsType::class, $envVars);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid() && $hasEnvFile) { 
            $data = $form->getData();

            if ($data['bannerFile']) {
                $statusBanner = $configService->move($data['bannerFile'], 'APP_BANNER');

                if ($statusBanner) {
                    $this->addFlash('success', "La bannière bien été modifiée.");
                } else {
                    $this->addFlash('error', "Le chargement de la nouvelle bannière a échoué.");
                }
            }

            if ($data['faviconFile']) {
                $statusFavicon = $configService->move($data['faviconFile'], 'APP_FAVICON');

                if ($statusFavicon) {
                    $this->addFlash('success', "Le favicon bien été modifié.");
                } else {
                    $this->addFlash('error', "Le chargement du nouveau favicon a échoué.");
                }
            }

            $status = $configService->save($data);

            if ($status) {
                $this->addFlash('success', "Les paramètres ont bien été enregistrés.");
            } else {
                $this->addFlash('error', "La sauvegarde a échoué, car le fichier de configuration .env.local est manquant.");
            }

            return $this->redirectToRoute('app_admin_settings_advanced');
        }

        return $this->render('admin/advanced_settings.html.twig', [
            'hasEnvFile' => $hasEnvFile,
            'form' => $form->createView(),
            'envVars' => $envVars,
            'bannerFile' => file_exists($envVars['APP_BANNER']) ? $envVars['APP_BANNER'] : null,
            'faviconFile' => file_exists($envVars['APP_FAVICON']) ? $envVars['APP_FAVICON'] : null,
            'activeAdvancedSettings' => true,
        ]);
    }


}
