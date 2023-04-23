<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($user, $subject);
                break;
            case self::DELETE:
                return $this->canDelete($user, $subject);
                break;
        }

        return false;
    }

    private function canEdit(UserInterface $user, User $subject): bool
    {
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return true;
        }

        if (in_array('ROLE_SUPER_ADMIN', $subject->getRoles())) {
            return false;
        }

        return true;
    }

    private function canDelete(UserInterface $user, User $subject): bool
    {
        if (in_array('ROLE_SUPER_ADMIN', $subject->getRoles())) {
            return false;
        } elseif (in_array('ROLE_ADMIN', $subject->getRoles()) && in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return true;
        }  elseif (in_array('ROLE_ADMIN', $subject->getRoles()) && !in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return false;
        }

        return true;
    }
}
