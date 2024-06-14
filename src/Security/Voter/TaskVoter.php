<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * TaskVoter is responsible for voting on task-related actions
 * such as view, edit, and delete permissions. It implements
 * logic to determine if a user has the necessary rights to
 * perform specific operations based on roles and ownership.
 */
class UserTaskVoter extends Voter
{
    public const CREATE = 'TASK_CREATE';
    public const DELETE = 'TASK_DELETE';
    public const LIST = 'TASK_LIST';
    public const EDIT = 'TASK_EDIT';
    public const TOGGLE = 'TASK_TOGGLE';

    /**
     * Method of voter.
     *
     * @param string $attribute Is the attribute determined if voter is true or false
     * @param mixed  $subject   the subject of the vote
     * @return bool
     **/
    protected function supports(string $attribute, mixed $subject): bool
    {
        return
        in_array($attribute, [self::CREATE, self::LIST])
        || (
            in_array($attribute, [self::EDIT, self::DELETE, self::TOGGLE])
            && $subject instanceof Task
        );
    }

    /**
     * Method of voter.
     *
     * @param string         $attribute Is the attribute determined if voter is true or false
     * @param mixed          $subject   the subject of the vote
     * @param TokenInterface $token     token of vote
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        $userRoles = $user->getRoles();

        if (in_array(null, $userRoles)) {
            return false;
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

        return false;
    }

    /**
     * Function to check Owner of Task.
     *
     * @param mixed          $subject the subject of voter
     * @param TokenInterface $token   The token
     * @return bool|AccessDeniedException
     */
    protected function checkOwner(mixed $subject, TokenInterface $token): bool|AccessDeniedException
    {
        $user = $token->getUser();
        $checkIdUser = $subject?->getOwner() === $user;

        return $checkIdUser ? $checkIdUser : throw new AccessDeniedException("Vous n'êtes pas le propriétaire de cette tâche.");
    }
}
