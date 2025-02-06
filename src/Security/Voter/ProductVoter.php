<?php

namespace App\Security\Voter;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

final class ProductVoter extends Voter
{
    public const CREATE = 'product_create';
    public const EDIT = 'product_edit';
    public const DELETE = 'product_delete';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::EDIT, self::DELETE])
            && $subject instanceof Product;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($user, $token);

            case self::EDIT:
                return $this->canEdit($user, $token);

            case self::DELETE:
                return $this->canDelete($user, $token);
        }

        return false;
    }

    private function canCreate(UserInterface $user, TokenInterface $token): bool
    {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']);
    }

    private function canEdit(UserInterface $user, TokenInterface $token): bool
    {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']);
    }

    private function canDelete(UserInterface $user, TokenInterface $token): bool
    {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']);
    }
}
