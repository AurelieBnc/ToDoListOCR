<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormFactoryInterface;

class UserManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly UserRepository $userRepository,
        private readonly FormFactoryInterface $formFactory,
    ) {
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
