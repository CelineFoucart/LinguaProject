<?php

namespace App\Twig\Runtime;

use App\Repository\CategoryRepository;
use Twig\Extension\RuntimeExtensionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoryExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function getCategories(): string
    {
        $categories = $this->categoryRepository->findParents();

        if (empty($categories)) {
            return '';
        }

        $html = '<li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Catégories
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">'
        ;

        foreach ($categories as $category) {
            $route = $this->urlGenerator->generate('app_category_show', ['slug' => $category->getSlug()]);
            $html .= '<a class="dropdown-item" href="'.$route.'">'.$category->getTitle().'</a>';
        }

        return $html . '</div></li>';
    }
}
