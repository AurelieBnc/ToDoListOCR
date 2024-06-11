<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;


class UserManager
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private UserRepository $userRepository;


    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher) 
    {
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->userPasswordHasher = $userPasswordHasher;

    }

    public function createUser(User $user, string $plainPassword): User
    {
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $plainPassword
            )
        );
        $this->userRepository->add($user);

        return $user;
    }

    public function editUser(User $user, string $plainPassword): User
    {        
        $newPasswordHashed = $this->userPasswordHasher->hashPassword(
            $user,
            $plainPassword
        );
        
        $this->userRepository->upgradePassword($user, $newPasswordHashed);
        $this->userRepository->update($user);

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $this->userRepository->remove($user, true);
    }
}
