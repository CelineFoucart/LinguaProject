<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Settings;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    private ?Settings $settings = null;

    public function __construct(SettingsRepository $settingsRepository)
    {
        $settings = $settingsRepository->findSettings();
        if (null === $settings) {
            $settings = (new Settings())->setLanguageTranslatedName("Une langue");
        }

        $this->settings = $settings;
    }

    #[Route('/article', name: 'app_article_index')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
            'is_article' => true,
            'settings' => $this->settings,
        ]);
    }

    #[Route('/category/{slug}', name: 'app_category_show')]
    public function section(Category $category): Response
    {
        return $this->render('article/category.html.twig', [
            'category' => $category,
            'is_article' => true,
            'settings' => $this->settings,
        ]);
    }

    #[Route('/article/{slug}', name: 'app_article_show')]
    public function article(Article $article): Response
    {
        return $this->render('article/article.html.twig', [
            'article' => $article,
            'is_article' => true,
            'settings' => $this->settings,
        ]);
    }
}
