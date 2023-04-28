<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Repository\CategoryRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SettingsRepository $settingsRepository, CategoryRepository $categoryRepository): Response
    {
        $settings = $settingsRepository->findSettings();
        if (null === $settings) {
            $settings = (new Settings())->setLanguageTranslatedName("Une langue");
        }
        
        $categories = $categoryRepository->findParents();

        return $this->render('home/index.html.twig', [
            'is_home' => true,
            'settings' => $settings,
            'categories' => $categories,
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(SettingsRepository $settingsRepository): Response
    {
        $settings = $settingsRepository->findSettings();
        if (null === $settings) {
            $settings = (new Settings())->setLanguageTranslatedName("Une langue");
        }

        return $this->render('home/about.html.twig', [
            'is_about' => true,
            'settings' => $settings,
        ]);
    }
}
