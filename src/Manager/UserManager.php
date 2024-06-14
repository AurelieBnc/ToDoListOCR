<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{

    private UserPasswordHasherInterface $userPasswordHasher;

    private readonly UserRepository $userRepository;

    /**
     * Construct with EntityManagerInterface and UserPasswordHasherInterface.
     *
     * @param EntityManagerInterface        $entityManager      Manage to create UserRepository
     * @param UserPasswordHasherInterface   $userPasswordHasher Hasher for password of User
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->userPasswordHasher = $userPasswordHasher;

    }

    /**
     * Function to create an user.
     *
     * @param User   $user          to create
     * @param string $plainPassword password no hashed from form
     * @return User
     */
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

    /**
     * Function to edit an user.
     *
     * @param User   $user          to edit
     * @param string $plainPassword password no hashed from form
     * @return User
     */
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

    /**
     * Function to delete an user.
     *
     * @param User $user to edit
     * @return void
     */
    public function deleteUser(User $user): void
    {
        $this->userRepository->remove($user, true);
    }
}
