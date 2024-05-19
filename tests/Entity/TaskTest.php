<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use \DateTimeImmutable;

class TaskTest extends TestCase
{
    public function testGetId()
    {
        $task = new Task();
        $this->assertNull($task->getId());
    }

    public function testGetAndSetTitle()
    {
        $task = new Task();
        $title = 'Test Title';
        $task->setTitle($title);
        $this->assertSame($title, $task->getTitle());
    }

    public function testGetAndSetContent()
    {
        $task = new Task();
        $content = 'Test Content';
        $task->setContent($content);
        $this->assertSame($content, $task->getContent());
    }

    public function testGetAndSetCreatedAt()
    {
        $task = new Task();
        $createdAt = new DateTimeImmutable();
        $task->setCreatedAt($createdAt);
        $this->assertSame($createdAt, $task->getCreatedAt());
    }

    public function testGetAndSetIsDone()
    {
        $task = new Task();
        $this->assertFalse($task->getIsDone());
        $task->setIsDone(true);
        $this->assertTrue($task->getIsDone());
    }

    public function testGetAndSetOwner()
    {
        $task = new Task();
        $user = $this->createMock(User::class);
        $task->setOwner($user);
        $this->assertSame($user, $task->getOwner());
    }

    public function testToggle()
    {
        $task = new Task();
        $task->toggle(true);
        $this->assertTrue($task->getIsDone());
        $task->toggle(false);
        $this->assertFalse($task->getIsDone());
    }
}
