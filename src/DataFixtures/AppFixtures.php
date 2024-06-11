<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\DataFixtures\DateFixtures;
use App\DataFixtures\TaskFixtures;
use App\EnumTodo\TaskStatus;

/**
 * @codeCoverageIgnore
 */
class AppFixtures extends Fixture
{
    private $listStatus;
    private $userPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->listStatus = [
            TaskStatus::IsDone,
            TaskStatus::Todo
        ];
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

        //création d'un user sans role
        $otherUser = new User();
        $otherUser->setUsername("User3");
        $otherUser->setEmail("user3@todolist.fr");
        $otherUser->setRoles([null]);
        $otherUser->setPassword($this->userPasswordHasher->hashPassword($otherUser, "password"));
        $manager->persist($otherUser);

        $userList[] = $otherUser;
        
        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setUsername("Admin");
        $userAdmin->setEmail("admin@todolist.fr");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin); 

        // Création de 20 tasks avec Owner
        $taskFixture = new TaskFixtures();
        $taskContentList = $taskFixture->TaskContentList();

        for ($i = 0; $i < 35; $i++) {
            $randomCreatedAt = (new DateFixtures())->randDate();
            $randomContentIndex = array_rand($taskContentList);
            $randomContent = $taskContentList[$randomContentIndex];
            $randomStatusIndex = array_rand($this->listStatus);
            $randomStatus = $this->listStatus[$randomStatusIndex];

            $task = new Task();
            $task->setTitle('Titre tache '.$i);
            $task->setContent($randomContent);
            // /** @var DateTimeImmutable $randomCreatedAt */
            $task->setCreatedAt($randomCreatedAt);
            $task->setOwner($userList[array_rand($userList)]);
            $task->setStatus($randomStatus);
            
            $manager->persist($task);
        }

        //Création de 5 tasks anonyme
        for ($i = 0; $i < 5; $i++) {
            $randomCreatedAt = (new DateFixtures())->randDate();

            $randomContentIndex = array_rand($taskContentList);
            $randomContent = $taskContentList[$randomContentIndex];
            $randomStatusIndex = array_rand($this->listStatus);
            $randomStatus = $this->listStatus[$randomStatusIndex];

            $task = new Task();
            $task->setTitle('Titre tache anonyme '.$i);
            $task->setContent($randomContent);
            // /** @var DateTimeImmutable $randomCreatedAt */
            $task->setCreatedAt($randomCreatedAt);
            $task->setStatus($randomStatus);
            
            $manager->persist($task);
        }

        // Création de 5 tasks stables
        //Utilisateur 1
        $randomCreatedAt = (new DateFixtures())->randDate();

        $randomContentIndex = array_rand($taskContentList);
        $randomContent = $taskContentList[$randomContentIndex];

        $task = new Task();
        $task->setTitle('Titre tache utilisateur 1');
        $task->setContent($randomContent);
        // /** @var DateTimeImmutable $randomCreatedAt */
        $task->setCreatedAt($randomCreatedAt);
        $task->setOwner($firstUser);
        $task->setStatus(TaskStatus::IsDone);

        $manager->persist($task);

        $randomCreatedAt = (new DateFixtures())->randDate();

        $randomContentIndex = array_rand($taskContentList);
        $randomContent = $taskContentList[$randomContentIndex];

        $task = new Task();
        $task->setTitle('Titre tache utilisateur 1 à éditer');
        $task->setContent($randomContent);
        // /** @var DateTimeImmutable $randomCreatedAt */
        $task->setCreatedAt($randomCreatedAt);
        $task->setOwner($firstUser);
        $task->setStatus(TaskStatus::IsDone);

        $manager->persist($task);

        $randomCreatedAt = (new DateFixtures())->randDate();

        $randomContentIndex = array_rand($taskContentList);
        $randomContent = $taskContentList[$randomContentIndex];

        $task = new Task();
        $task->setTitle('Titre tache utilisateur 1 à supprimer');
        $task->setContent($randomContent);
        // /** @var DateTimeImmutable $randomCreatedAt */
        $task->setCreatedAt($randomCreatedAt);
        $task->setOwner($firstUser);
        $task->setStatus(TaskStatus::IsDone);

        $manager->persist($task);

        //Utilisateur 2
        $randomCreatedAt = (new DateFixtures())->randDate();

        $randomContentIndex = array_rand($taskContentList);
        $randomContent = $taskContentList[$randomContentIndex];

        $task = new Task();
        $task->setTitle('Titre tache utilisateur 2');
        $task->setContent($randomContent);
        // /** @var DateTimeImmutable $randomCreatedAt */
        $task->setCreatedAt($randomCreatedAt);
        $task->setOwner($secondUser);
        $task->setStatus(TaskStatus::Todo);

        $manager->persist($task);

        $randomCreatedAt = (new DateFixtures())->randDate();

        $randomContentIndex = array_rand($taskContentList);
        $randomContent = $taskContentList[$randomContentIndex];

        $task = new Task();
        $task->setTitle('Titre tache utilisateur 2 à supprimer');
        $task->setContent($randomContent);
        // /** @var DateTimeImmutable $randomCreatedAt */
        $task->setCreatedAt($randomCreatedAt);
        $task->setOwner($secondUser);
        $task->setStatus(TaskStatus::Todo);

        $manager->persist($task);
        
        $manager->flush();
    }
}
