<?php

namespace App\Tests\Security;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test the access with role Admin
 */
class AccessAdminTest extends WebTestCase
{
    
    private KernelBrowser $client;
    private TaskRepository $taskRepository;
    private UserRepository $userRepository;
    private User $user;
    private User $admin;
    private Task $anonymousTask;


    protected function setUp(): void
    {
        $this->client = static::createClient([],
            [
                'HTTP_HOST' => 'localhost',
                'HTTPS' => false,
            ]
        );
        $this->taskRepository = $this->client->getContainer()->get(TaskRepository::class);
        
        $task = $this->taskRepository->findOneByTitle('Titre tache anonyme 1');
        $this->anonymousTask = $task;

        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
 
        $user = $this->userRepository->findOneByEmail('user1@todolist.fr');
        $this->user = $user;
        $admin = $this->userRepository->findOneByEmail('admin@todolist.fr');
        $this->admin = $admin;
    }

    public function testEditTaskWithOutOwnerAndWithRoleUser(): void
    {
        $this->client->loginUser($this->user, 'secured_area');

        $this->client->request('GET', '/tasks/'.$this->anonymousTask->getId().'/edit');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testEditTaskWithOutOwnerAndWithRoleAdmin(): void
    {
        $this->client->loginUser($this->admin, 'secured_area');

        $this->client->request('GET', '/tasks/'.$this->anonymousTask->getId().'/edit');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('button', 'Modifier');
    }

    public function testAnonymousTaskDeleteWithUnauthorizedUser(): void
    {
        $this->client->loginUser($this->user, 'secured_area');

        $this->client->request('GET', '/tasks/'.$this->anonymousTask->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testTaskDeleteWithRoleAdminInOtherOwner(): void
    {
        $this->client->followRedirects();
        $this->client->loginUser($this->admin, 'secured_area');
        $taskToDelete = $this->taskRepository->findOneByTitle('Titre tache utilisateur 2 à supprimer');

        $this->client->request('GET', '/tasks/'.$taskToDelete->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }

    public function testToggleTaskWithOutOwnerAndWithRoleAdmin(): void
    {
        $this->client->followRedirects();
        $this->client->loginUser($this->admin, 'secured_area');

        $this->client->request('GET', '/tasks/'.$this->anonymousTask->getId().'/toggle');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }

}
