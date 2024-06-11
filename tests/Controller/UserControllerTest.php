<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{    //public static function setUpBeforeClass(): void => pour charge une fois au début et garder le dérouler // pour le fonctionnel
    //rechercher comment charger la base de données dans le setup::beforeClass
    // use FixturesTrait;

    private KernelBrowser $client;
    private UserRepository $userRepository;
    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        $this->client = static::createClient([], [
            'HTTP_HOST' => 'localhost',
            'HTTPS' => false,
        ]);
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
 
        $user = $this->userRepository->findOneByEmail('user1@todolist.fr');
        $this->user = $user;

        $admin = $this->userRepository->findOneByEmail('admin@todolist.fr');
        $this->admin = $admin;
        
        $this->client->loginUser($this->admin, 'secured_area');
    }

    public function testUserListWithUnauthorizedAccess(): void
    {
        $this->client->loginUser($this->user, 'secured_area');

        $this->client->request('GET', '/users/list');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testUserListWithAuthorizedAccess(): void
    {
        $crawler = $this->client->request('GET', '/users/list');
        $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertCount(4, $crawler->filter('.user'));
    }

    public function testUserListWithPageUnvalid(): void
    {
        $this->client->request('GET', '/users/list?page=0');
        $this->client->getResponse();

        $this->assertResponseIsSuccessful();
    }

    public function testCreateUserWithUnauthorizedAccess(): void
    {
        $this->client->loginUser($this->user, 'secured_area');

        $this->client->request('GET', '/users/create');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testCreateUserWithAuthorizedAccess(): void
    {
        $this->client->loginUser($this->admin, 'secured_area');

        $this->client->request('GET', '/users/create');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('button', 'Ajouter');
    }

    public function testCreateUsertWithData()
    {   
        $this->client->followRedirects();
        
        $this->client->loginUser($this->admin, 'secured_area'); 

        $crawler = $this->client->request('GET', '/users/create');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Créer un utilisateur');
        $this->assertSelectorTextContains('button', 'Ajouter');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'Username';
        $form['user[email]'] = 'username@todolist.fr';
        $form['user[plainPassword][first]'] = 'password';
        $form['user[plainPassword][second]'] = 'password';
        $form['user[roles]']->select(1);

        $crawler = $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertCount(5, $crawler->filter('.user'));
    }

    public function testEditUserWithUnauthorizedAccess(): void
    {
        $this->client->loginUser($this->user, 'secured_area');

        $this->client->request('GET', '/users/'.$this->user->getId().'/edit');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testEditUserWithAuthorizedAccess(): void
    {
        $this->client->request('GET', '/users/' . $this->user->getId() . '/edit');
        $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Modifier');
        $this->assertSelectorTextContains('h1', 'Modifier '. $this->user->getUsername());
    }

    public function testEditUsertWithNewData()
    {
        $userToTest = $this->userRepository->findOneByEmail('user2@todolist.fr');

        $this->client->followRedirects();
        $crawler = $this->client->request('GET', "/users/".$userToTest->getId()."/edit");
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'usernameUpdated';
        $form['user[plainPassword][first]'] = 'newPassword';
        $form['user[plainPassword][second]'] = 'newPassword';

        $crawler = $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertCount(5, $crawler->filter('.user'));
    }

    public function testUserDeleteWithUnauthorizedAccess()
    {
        $this->client->loginUser($this->user, 'secured_area');

        $this->client->request('GET', '/users/'.$this->user->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testUserDeleteWithAuthorizedAccess()
    {
        $this->client->followRedirects();
        $user = $this->userRepository->findOneByEmail('user2@todolist.fr');

        $crawler = $this->client->request('GET', '/users/' . $user->getId() . '/delete');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertCount(4, $crawler->filter('.user'));
    }
}
