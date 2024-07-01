<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AccessUserByUserTest extends WebTestCase
{

    private KernelBrowser $client;

    private UserRepository $userRepository;

    private User $user;

    /**
     * We setup an user.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient([], ['HTTP_HOST' => 'localhost', 'HTTPS' => false]);
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);

        $user = $this->userRepository->findOneByEmail('user1@todolist.fr');
        $this->user = $user;

        $this->client->loginUser($this->user, 'secured_area');

    }

    /**
     * I access the list of users with unauthorized access.
     *
     * @return void
     */
    public function testUserListWithUnauthorizedAccess(): void
    {
        $this->client->request('GET', '/users/list');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());

    }

    /**
     * I create a user with unauthorized access.
     *
     * @return void
     */
    public function testCreateUserWithUnauthorizedAccess(): void
    {
        $this->client->request('GET', '/users/create');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());

    }

    /**
     * I edit a user with unauthorized access.
     *
     * @return void
     */
    public function testEditUserWithUnauthorizedAccess(): void
    {
        $this->client->request('GET', '/users/'.$this->user->getId().'/edit');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());

    }

    /**
     * I delete a user with authorized access.
     *
     * @return void
     */
    public function testUserDeleteWithUnauthorizedAccess()
    {
        $this->client->request('GET', '/users/'.$this->user->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());

    }
}
