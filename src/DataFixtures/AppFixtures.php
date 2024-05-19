<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\DataFixtures\DateFixtures;
use App\DataFixtures\TaskFixtures;
use DateTimeImmutable;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $userList = [];

        // Création d'un user
        $firstUser = new User();
        $firstUser->setUsername("User1");
        $firstUser->setEmail("user1@todolist.fr");
        $firstUser->setRoles(["ROLE_USER"]);
        $firstUser->setPassword($this->userPasswordHasher->hashPassword($firstUser, "password"));
        $manager->persist($firstUser);

        $userList[] = $firstUser;

        // Création d'un second user
        $secondUser = new User();
        $secondUser->setUsername("User2");
        $secondUser->setEmail("user2@todolist.fr");
        $secondUser->setRoles(["ROLE_USER"]);
        $secondUser->setPassword($this->userPasswordHasher->hashPassword($secondUser, "password"));
        $manager->persist($secondUser);

        $userList[] = $secondUser;
        
        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setUsername("Admin");
        $userAdmin->setEmail("admin@todolist.fr");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin); 

        // create 20 tasks
        $taskFixture = new TaskFixtures();
        $taskContentList = $taskFixture->TaskContentList();

        for ($i = 0; $i < 35; $i++) {
            $randomCreatedAt = (new DateFixtures())->randDate();

            $randomContentIndex = array_rand($taskContentList);
            $randomContent = $taskContentList[$randomContentIndex];

            $task = new Task();
            $task->setTitle('Titre tache '.$i);
            $task->setContent($randomContent);
            // /** @var DateTimeImmutable $randomCreatedAt */
            $task->setCreatedAt($randomCreatedAt);
            $task->setOwner($userList[array_rand($userList)]);
            $task->setIsDone((bool)rand(0,1));
            $manager->persist($task);
        }

        $manager->flush();
    }
}
