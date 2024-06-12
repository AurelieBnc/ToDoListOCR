<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{

    private KernelBrowser $client;

    
    protected function setUp(): void
    {
        $this->client = static::createClient(
            [],
            [
                'HTTP_HOST' => 'localhost',
                'HTTPS' => false,
            ]
        );

    }

    public function testLogin(): void
    {
        $this->client->followRedirects();

        $this->client->request('GET', '/tasks/create');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testLogOut(): void
    {
        $this->client->followRedirects();

        $this->client->request('GET', '/logout');
        $response = $this->client->getResponse();
 
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !');
    }

}
