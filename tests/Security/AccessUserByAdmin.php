<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{

    private KernelBrowser $client;
    private UserRepository $userRepository;
    private User $user;
    private User $admin;


    /**
     * We set up a user and an admin.
     * 
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient(
            [],
            [
                'HTTP_HOST' => 'localhost',
                'HTTPS' => false,
            ]
        );
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);

        $user = $this->userRepository->findOneByEmail('user1@todolist.fr');
        $this->user = $user;

        $admin = $this->userRepository->findOneByEmail('admin@todolist.fr');
        $this->admin = $admin;

        $this->client->loginUser($this->admin, 'secured_area');

    }

    /**
     * I access the list of users with authorized access.
     * 
     * @return void
     */
    public function testUserListWithAuthorizedAccess(): void
    {
        $crawler = $this->client->request('GET', '/users/list');
        $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertCount(4, $crawler->filter('.user'));

    }

    /**
     * I create a user with authorized access.
     * 
     * @return void
     */
    public function testCreateUserWithAuthorizedAccess(): void
    {
        $this->client->loginUser($this->admin, 'secured_area');

        $this->client->request('POST', '/users/create');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('button', 'Ajouter');

    }

    /**
     * I edit a user with authorized access.
     * 
     * @return void
     */
    public function testEditUserWithAuthorizedAccess(): void
    {
        $this->client->request('POST', '/users/'.$this->user->getId().'/edit');
        $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Modifier');
        $this->assertSelectorTextContains('h1', 'Modifier '.$this->user->getUsername());

    }

    /**
     * I delete a user with authorized access.
     * 
     * @return void
     */
    public function testUserDeleteWithAuthorizedAccess(): void
    {
        $this->client->followRedirects();
        $user = $this->userRepository->findOneByEmail('user2@todolist.fr');

        $crawler = $this->client->request('DELETE', '/users/'.$user->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertCount(4, $crawler->filter('.user'));

    }

}
