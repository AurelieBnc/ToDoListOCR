<?php

namespace App\Tests\Security;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AccessUserTest extends WebTestCase
{
    private KernelBrowser $client;
    private TaskRepository $taskRepository;
    private UserRepository $userRepository;
    private User $user;
    private User $userWithoutRole;
    private Task $taskUser1;
    private Task $taskUser2;
    private Task $task;


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

        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
 
        $user = $this->userRepository->findOneByEmail('user1@todolist.fr');
        $this->user = $user;
        $user = $this->userRepository->findOneByEmail('user3@todolist.fr');
        $this->userWithoutRole = $user;
    }

    public function testCreateTaskWithUnauthorizedAccess(): void
    {
        $this->client->followRedirects();

        $this->client->request('GET', '/tasks/create');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testCreateTaskWithAuthorizedAccess(): void
    {
        $this->client->loginUser($this->user, 'secured_area');

        $this->client->request('GET', '/tasks/create');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('button', 'Ajouter');
    }

    public function testCreateTasktWithDataWithUserWithoutRole()
    {   
        $this->client->followRedirects();
        
        $this->client->loginUser($this->userWithoutRole, 'secured_area'); 
        $this->client->request('GET', '/tasks/create');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testEditTaskWithUnauthorizedAccess(): void
    {
        $this->client->followRedirects();

        $this->client->request('GET', '/tasks/'.$this->task->getId().'/edit');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testEditTasktWitUnauthorizedUser()
    {
        $this->client->loginUser($this->user, 'secured_area');

        $this->client->request('GET', '/tasks/'.$this->taskUser2->getId().'/edit');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testEditTasktWithAuthorizedUser()
    {
        $this->client->loginUser($this->user, 'secured_area');
        $this->client->request('GET', '/tasks/'.$this->taskUser1->getId().'/edit');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('button', 'Modifier');
    }

    public function testTaskDeleteWithUnauthorizedAccess()
    {        $this->client->followRedirects();
        $this->client->request('GET', '/tasks/'.$this->task->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testTaskDeleteWithUnauthorizedUser()
    {
        $this->client->loginUser($this->user, 'secured_area');

        $this->client->request('GET', '/tasks/'.$this->taskUser2->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testTaskDeleteWithAuthorizedAccess()
    {
        $this->client->followRedirects();
        $this->client->loginUser($this->user, 'secured_area'); 
        $taskToDelete = $this->taskRepository->findOneByTitle('Titre tache utilisateur 1 à supprimer');

        $this->client->request('GET', '/tasks/'.$taskToDelete->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }
}
