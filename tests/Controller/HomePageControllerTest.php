<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/homepage');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !');
    
        if ($client->getContainer()->get('security.authorization_checker')->isGranted('TASK_CREATE')) {
            $this->assertSelectorExists('a.btn.btn-success');
        }

        if ($client->getContainer()->get('security.authorization_checker')->isGranted('TASK_LIST')) {
            $this->assertSelectorExists('a.btn.btn-info.text-white');
            $this->assertSelectorExists('a.btn.text-info.fw-bold');
        }
    }
}
