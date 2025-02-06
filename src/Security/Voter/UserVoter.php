<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

final class UserVoter extends Voter
{
    public const LIST = 'USER_LIST';
    public const ADD = 'USER_ADD';
    public const EDIT = 'USER_EDIT';
    public const VIEW = 'USER_VIEW';
    public const DELETE = 'USER_DELETE';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::LIST, self::ADD, self::EDIT, self::VIEW, self::DELETE])
            && ($subject instanceof User || $subject === null);
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
            case self::LIST:
                return $this->canList($user);

            case self::ADD:
                return $this->canAdd($user);

            case self::EDIT:
                return $this->canEdit($user, $subject);

            case self::VIEW:
                return $this->canView($user, $subject);

            case self::DELETE:
                return $this->canDelete($user, $subject);
        }

        return false;
    }

    private function canList(UserInterface $user): bool
    {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']);
    }

    private function canAdd(UserInterface $user): bool
    {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']);
    }

    private function canEdit(UserInterface $user, User $subject): bool
    {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) || $user === $subject;
    }

    private function canView(UserInterface $user, User $subject): bool
    {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) || $user === $subject;
    }

    private function canDelete(UserInterface $user, User $subject): bool
    {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']);
    }
}
