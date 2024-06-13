<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\DataFixtures\DateFixtures;
use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
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

    /**
     * Load :
     *      - 3 users  + 1 admin
     *      - 20 tasks avec Owner
     *      - 5 tasks anonyme
     *      - 3 tasks stables - user1
     *      - 2 tasks stables - user2
     * 
     * @param ObjectManager $manager manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {

        $userFixture = new UserFixtures($this->userPasswordHasher);
        $userList = $userFixture->UserList($manager);

        $firstUser = $userList[1];
        $secondUser = $userList[2];


        // Création de 20 tasks avec Owner.
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
            $task->setCreatedAt($randomCreatedAt);
            $task->setOwner($userList[array_rand($userList)]);
            $task->setStatus($randomStatus);
            
            $manager->persist($task);
        }

        // Création de 5 tasks anonyme
        for ($i = 0; $i < 5; $i++) {
            $randomCreatedAt = (new DateFixtures())->randDate();

            $randomContentIndex = array_rand($taskContentList);
            $randomContent = $taskContentList[$randomContentIndex];
            $randomStatusIndex = array_rand($this->listStatus);
            $randomStatus = $this->listStatus[$randomStatusIndex];

            $task = new Task();
            $task->setTitle('Titre tache anonyme '.$i);
            $task->setContent($randomContent);
            $task->setCreatedAt($randomCreatedAt);
            $task->setStatus($randomStatus);
            
            $manager->persist($task);
        }

        // Création de 5 tasks stables
        // Utilisateur 1
        $randomCreatedAt = (new DateFixtures())->randDate();

        $randomContentIndex = array_rand($taskContentList);
        $randomContent = $taskContentList[$randomContentIndex];

        $task = new Task();
        $task->setTitle('Titre tache utilisateur 1');
        $task->setContent($randomContent);
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
        $task->setCreatedAt($randomCreatedAt);
        $task->setOwner($firstUser);
        $task->setStatus(TaskStatus::IsDone);

        $manager->persist($task);

        // Utilisateur 2
        $randomCreatedAt = (new DateFixtures())->randDate();

        $randomContentIndex = array_rand($taskContentList);
        $randomContent = $taskContentList[$randomContentIndex];

        $task = new Task();
        $task->setTitle('Titre tache utilisateur 2');
        $task->setContent($randomContent);
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
        $task->setCreatedAt($randomCreatedAt);
        $task->setOwner($secondUser);
        $task->setStatus(TaskStatus::Todo);

        $manager->persist($task);
        
        $manager->flush();

    }

}
