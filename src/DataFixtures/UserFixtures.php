<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @codeCoverageIgnore
 */
class UserFixtures
{
    private $userPasswordHasher;

    /**
     * Construct with UserPasswordHasherInterface.
     *
     * @param UserPasswordHasherInterface $userPasswordHasher userPasswordHasher
     */
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * List of Content for task list.
     *
     * @param ObjectManager $manager to create User
     *
     * @return array<User>
     */
    public function UserList(ObjectManager $manager): array
    {
        $userList = [];

        // Création d'un user.
        $firstUser = new User();
        $firstUser->setUsername('User1');
        $firstUser->setEmail('user1@todolist.fr');
        $firstUser->setRoles(['ROLE_USER']);
        $firstUser->setPassword($this->userPasswordHasher->hashPassword($firstUser, 'password'));
        $manager->persist($firstUser);

        $userList[] = $firstUser;

        // Création d'un second user.
        $secondUser = new User();
        $secondUser->setUsername('User2');
        $secondUser->setEmail('user2@todolist.fr');
        $secondUser->setRoles(['ROLE_USER']);
        $secondUser->setPassword($this->userPasswordHasher->hashPassword($secondUser, 'password'));
        $manager->persist($secondUser);

        $userList[] = $secondUser;

        // Création d'un user sans role.
        $otherUser = new User();
        $otherUser->setUsername('User3');
        $otherUser->setEmail('user3@todolist.fr');
        $otherUser->setRoles([null]);
        $otherUser->setPassword($this->userPasswordHasher->hashPassword($otherUser, 'password'));
        $manager->persist($otherUser);

        $userList[] = $otherUser;

        // Création d'un user admin.
        $userAdmin = new User();
        $userAdmin->setUsername('Admin');
        $userAdmin->setEmail('admin@todolist.fr');
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, 'password'));
        $manager->persist($userAdmin);

        return $userList;
    }
}
