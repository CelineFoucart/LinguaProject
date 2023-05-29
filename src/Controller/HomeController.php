<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Settings;
use App\Repository\CategoryRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    public function __construct(private SettingsRepository $settingsRepository)
    {
        
    }
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findParents();

        return $this->render('home/index.html.twig', [
            'is_home' => true,
            'settings' => $this->getSettings(),
            'categories' => $categories,
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'settings' => $this->getSettings(),
            'is_page' => true,
        ]);
    }

    #[Route('/page/{slug}', name: 'app_page')]
    public function page(Page $page): Response
    {
        return $this->render('home/page.html.twig', [
            'page' => $page,
            'is_page' => true,
        ]);
    }

    private function getSettings(): Settings
    {
        $settings = $this->settingsRepository->findSettings();
        if (null === $settings) {
            $settings = new Settings();
        }

        return $settings;
    }
}
