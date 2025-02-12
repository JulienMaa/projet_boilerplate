<?php

namespace App\Security\Voter;

use App\Entity\Client;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientVoter extends Voter
{
    const VIEW = 'view_client';
    const CREATE = 'create_client';
    const EDIT = 'edit_client';
    const DELETE = 'delete_client';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::CREATE, self::EDIT, self::DELETE]) && ($subject instanceof Client || $subject === null);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::VIEW => in_array('ROLE_MANAGER', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles()),
            self::CREATE => in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_MANAGER', $user->getRoles()),
            self::EDIT => in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_MANAGER', $user->getRoles()),
            self::DELETE => in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_MANAGER', $user->getRoles()),
            default => false,
        };
    }
}

?>
