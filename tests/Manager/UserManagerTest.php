<?php

namespace App\Tests\Manager;

use App\Entity\User;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase ;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManagerTest extends WebTestCase
{
    private $client;
    private $userRepository;
    private $user;
    private $admin;
    private $stubUserPasswordHasher;
    private $sut;
    private $mockUserRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient([], [
            'HTTP_HOST' => 'localhost',
            'HTTPS' => false,
        ]);
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
        $this->stubUserPasswordHasher = $this->createStub(UserPasswordHasherInterface::class);
        $this->mockUserRepository = $this->createMock(UserRepository::class);

        $this->sut = new UserManager(
            $this->createMock(EntityManagerInterface::class),
            $this->stubUserPasswordHasher,   
            $this->mockUserRepository,    
            $this->createMock(FormFactoryInterface::class),  
        );
 
        $user = $this->userRepository->findOneByEmail('user1@todolist.fr');
        $this->user = $user;

        $admin = $this->userRepository->findOneByEmail('admin@todolist.fr');
        $this->admin = $admin;
        
        $this->client->loginUser($this->admin, 'secured_area');
    }

    public function testCreateUserShouldGeneratePassword()
    {
        $this->stubUserPasswordHasher->method('hashPassword')
            ->willReturn('hashedpassword');

        $user = new User;
        $this->mockUserRepository->expects($this->once())
            ->method('add')
            ->with($user);
        $createdUser = $this->sut->createUser($user, 'test');

        $this->assertEquals('hashedpassword', $createdUser->getPassword());
    }

    public function testEditUserShouldHaveSameId()
    {
        $this->stubUserPasswordHasher->method('hashPassword')
            ->willReturn('hashedpassword');

        $this->mockUserRepository->expects($this->once())
            ->method('update')
            ->with($this->user);

        $userWithNewDatas = new User;
        $userWithNewDatas->setUsername('usernameUpdated');
        $userWithNewDatas->setPassword('password');
        $userWithNewDatas->setEmail('user2zeaz@todolist.fr');
        $userWithNewDatas->setRoles(['ROLE_ADMIN']);

        $updatedUser = $this->sut->editUser($this->user, $userWithNewDatas);

        $this->assertEquals($this->user->getId(), $updatedUser->getId());
        $this->assertEquals('usernameUpdated', $updatedUser->getUsername());
    }

    public function testDeleteUserShouldCallRemove()
    {
        $user = new User;
        $this->mockUserRepository->expects($this->once())
        ->method('remove')
        ->with($user);

        $this->sut->deleteUser($user);
    }
}
