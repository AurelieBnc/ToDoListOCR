<?php

namespace App\Security\Voter;


use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const CREATE = 'USER_CREATE';
    public const DELETE = 'USER_DELETE';
    public const LIST = 'USER_LIST';
    public const EDIT = 'USER_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return
        in_array($attribute, [self::CREATE, self::LIST]) ||
        (
            in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof User
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        $userRoles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $userRoles)) {
            return $attribute === self::CREATE || $attribute === self::EDIT || $attribute === self::DELETE || $attribute === self::LIST
            ?? false;
        }

        return false;
    }
}