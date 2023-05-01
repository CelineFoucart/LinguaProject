<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\CategoryExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoryExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_categories', [CategoryExtensionRuntime::class, 'getCategories'], ['is_safe' => ['html']]),
        ];
    }
}
