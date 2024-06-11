<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TaskRepository $taskRepository;
    private UserRepository $userRepository;
    private User $user;
    private Task $taskUser1;
    private Task $taskUser2;
    private Task $task;
    private Task $anonymousTask;


    protected function setUp(): void
    {
        $this->client = static::createClient([], [
            'HTTP_HOST' => 'localhost',
            'HTTPS' => false,
        ]);
        $this->taskRepository = $this->client->getContainer()->get(TaskRepository::class);
        
        $task = $this->taskRepository->findOneByTitle('Titre tache Utilisateur 1');
        $this->taskUser1 = $task;
        $task = $this->taskRepository->findOneByTitle('Titre tache Utilisateur 2');
        $this->taskUser2 = $task;
        $task = $this->taskRepository->findOneByTitle('Titre tache 1');
        $this->task = $task;
        $task = $this->taskRepository->findOneByTitle('Titre tache anonyme 1');
        $this->anonymousTask = $task;

        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
 
        $user = $this->userRepository->findOneByEmail('user1@todolist.fr');
        $this->user = $user;
    }

    public function testCreateTasktWithData()
    {   
        $this->client->followRedirects();
        
        $this->client->loginUser($this->user, 'secured_area'); 

        $crawler = $this->client->request('GET', '/tasks/create');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('button', 'Ajouter');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Title task create';
        $form['task[content]'] = 'Content of task';
        $form['task[status]'] = 'todo';

        $crawler = $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }

    public function testEditTasktWithNewData()
    {   
        $this->client->followRedirects();
        
        $this->client->loginUser($this->user, 'secured_area'); 
        $taskToEdit = $this->taskRepository->findOneByTitle('Titre tache utilisateur 1 à éditer');

        $crawler = $this->client->request('GET', '/tasks/'.$taskToEdit->getId().'/edit');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('button', 'Modifier');

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Title task edit';

        $crawler = $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }

    public function testToggleTaskWithUnauthorizedAccess(): void
    {
        $this->client->followRedirects();

        $this->client->request('GET', '/tasks/'.$this->task->getId().'/toggle');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testToggleTasktWitOtherUser()
    {        
        $this->client->followRedirects();
        $this->client->loginUser($this->user, 'secured_area');

        $this->client->request('GET', '/tasks/'.$this->taskUser2->getId().'/toggle');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }

    public function testToggleTaskWithOutOwnerAndWithRoleUser()
    {
        $this->client->followRedirects();
        $this->client->loginUser($this->user, 'secured_area');

        $this->client->request('GET', '/tasks/'.$this->anonymousTask->getId().'/toggle');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }

    public function testToggleTasktWithOwningUser()
    {
        $this->client->followRedirects();
        $this->client->loginUser($this->user, 'secured_area');
        $this->client->request('GET', '/tasks/'.$this->taskUser1->getId().'/toggle');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }
}
