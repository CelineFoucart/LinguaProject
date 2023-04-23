<?php

namespace App\Twig\Extension;

use App\Twig\Security\AccessCheckRuntimeExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AccessCheckRuntimeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_role', [AccessCheckRuntimeExtensionRuntime::class, 'hasRole']),
        ];
    }
}
