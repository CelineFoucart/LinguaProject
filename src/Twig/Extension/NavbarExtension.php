<?php

namespace App\Twig\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use App\Twig\Runtime\PageExtensionRuntime;
use App\Twig\Runtime\CategoryExtensionRuntime;

class NavbarExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_categories', [CategoryExtensionRuntime::class, 'getCategories'], ['is_safe' => ['html']]),
            new TwigFunction('get_pages', [PageExtensionRuntime::class, 'getPages'], ['is_safe' => ['html']]),
        ];
    }
}
