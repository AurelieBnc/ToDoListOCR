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
    private User $admin;


    /**
     * We setup an admin user.
     * 
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient([], ['HTTP_HOST' => 'localhost','HTTPS' => false]);
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);

        $admin = $this->userRepository->findOneByEmail('admin@todolist.fr');
        $this->admin = $admin;

        $this->client->loginUser($this->admin, 'secured_area');

    }

    public function testUserListWithPageUnvalid(): void
    {
        $this->client->request('GET', '/users/list?page=0');
        $this->client->getResponse();

        $this->assertResponseIsSuccessful();

    }

    /**
     * I create a new user with data.
     * 
     * @return void
     */
    public function testCreateUsertWithData(): void
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

    /**
     * I edit a user with new data.
     * 
     * @return void
     */
    public function testEditUsertWithNewData(): void
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

}
