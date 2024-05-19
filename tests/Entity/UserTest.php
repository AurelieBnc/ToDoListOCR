<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetId()
    {
        $user = new User();
        $this->assertNull($user->getId());
    }

    public function testGetAndSetEmail()
    {
        $user = new User();
        $email = 'test@example.com';
        $user->setEmail($email);
        $this->assertSame($email, $user->getEmail());
    }

    public function testGetAndSetUsername()
    {
        $user = new User();
        $username = 'testuser';
        $user->setUsername($username);
        $this->assertSame($username, $user->getUsername());
    }

    public function testGetUserIdentifier()
    {
        $user = new User();
        $username = 'testuser';
        $user->setUsername($username);
        $this->assertSame($username, $user->getUserIdentifier());
    }

    public function testGetAndSetPassword()
    {
        $user = new User();
        $password = 'password';
        $user->setPassword($password);
        $this->assertSame($password, $user->getPassword());
    }

    public function testGetAndSetRoles()
    {
        $user = new User();
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $user->setRoles($roles);
        $this->assertSame(array_unique($roles), $user->getRoles());
    }

    public function testEraseCredentials()
    {
        $user = new User();
        $user->eraseCredentials();
        $this->assertTrue(true);
    }

    public function testAddTask()
    {
        $user = new User();
        $task = $this->createMock(Task::class);

        $task->expects($this->once())
             ->method('setOwner')
             ->with($this->equalTo($user));

        $user->addTask($task);
        $this->assertCount(1, $user->getTasks());
        $this->assertTrue($user->getTasks()->contains($task));
    }
}
