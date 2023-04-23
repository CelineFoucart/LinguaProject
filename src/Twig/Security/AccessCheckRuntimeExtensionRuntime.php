<?php

namespace App\Twig\Security;

use Twig\Extension\RuntimeExtensionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class AccessCheckRuntimeExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        protected RoleHierarchyInterface $roleHierarchy
    ) {
    }

    public function hasRole(UserInterface $user, string $role): bool
    {
        return in_array($role, $this->roleHierarchy->getReachableRoleNames($user->getRoles()));
    }
}
