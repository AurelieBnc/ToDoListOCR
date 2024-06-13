<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * UserVoter is responsible for voting on user-related actions
 * such as view, edit, and delete permissions. It implements
 * logic to determine if a user has the necessary rights to
 * perform specific operations based on roles and ownership.
 *
 * @return void
 */
class UserVoter extends Voter
{
    public const CREATE = 'USER_CREATE';
    public const DELETE = 'USER_DELETE';
    public const LIST = 'USER_LIST';
    public const EDIT = 'USER_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return
        in_array($attribute, [self::CREATE, self::LIST])
        || (
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
            return self::CREATE === $attribute || self::EDIT === $attribute || self::DELETE === $attribute || self::LIST === $attribute
            ?? false;
        }

        return false;
    }
}
