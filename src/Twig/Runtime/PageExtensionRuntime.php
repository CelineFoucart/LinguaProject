<?php

namespace App\Twig\Runtime;

use App\Repository\PageRepository;
use Twig\Extension\RuntimeExtensionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PageExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private PageRepository $pageRepository,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function getPages(): string
    {
        $pages = $this->pageRepository->findBy([], ['title' => 'ASC']);

        $html = '<li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarPageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Pages
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarPageDropdown">
            '
        ;

        $html .= '<li><a class="dropdown-item" href="'.$this->urlGenerator->generate('app_about').'">A propos</a></li>';

        if (!empty($pages)) {
            $html .= '<li><hr class="dropdown-divider"></li>';

            foreach ($pages as $page) {
                $route = $this->urlGenerator->generate('app_page', ['slug' => $page->getSlug()]);
                $html .= '<li><a class="dropdown-item" href="'.$route.'">'.$page->getTitle().'</a></li>';
            }
        }

        return $html . '</ul></li>';
    }
}