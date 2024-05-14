<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TaskVoter extends Voter
{
    public const CREATE = 'TASK_CREATE';
    public const DELETE = 'TASK_DELETE';
    public const LIST = 'TASK_LIST';
    public const EDIT = 'TASK_EDIT';
    public const TOGGLE = 'TASK_TOGGLE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return
        in_array($attribute, [self::CREATE, self::LIST]) ||
        (
            in_array($attribute, [self::EDIT, self::DELETE, self::TOGGLE])
            && $subject instanceof Task
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
            switch ($attribute) {
                case self::DELETE:
                case self::EDIT:
                case self::LIST:
                case self::CREATE:
                case self::TOGGLE:
                    return true;
                    break;
            }
        }

        if (in_array('ROLE_USER', $userRoles)) {
            switch ($attribute) {
                case self::DELETE:
                    return $this->checkOwner($subject, $token);
                case self::EDIT:
                    return $this->checkOwner($subject, $token);
                    break;
                case self::LIST:
                case self::CREATE:
                case self::TOGGLE:
                    return true;
                    break;
            }
        }
        return false;
    }

    protected function checkOwner(mixed $subject, TokenInterface $token): bool|AccessDeniedException
    {
        $user = $token->getUser();
        $checkIdUser = $subject?->getOwner() === $user;

        return $checkIdUser ? $checkIdUser : throw new AccessDeniedException("Vous n'êtes pas le propriétaire de cette tâche.");
    }
}