<?php

namespace tests\AppBundle\Form;

use App\Entity\Task;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

/**
 * Unit testUnit test of the UserType form.
 */
class UserTypeTest extends TypeTestCase
{
    /**
     * Test valid data.
     */
    public function testLoginSubmitValidData(): void
    {
        $userToTest = new User();

        // Create a list of tasks
        $task1 = new Task();
        $task1->setTitle('Task 1');
        $task1->setContent('Task 1 content');

        $task2 = new Task();
        $task2->setTitle('Task 2');
        $task2->setContent('Task 2 content');

        $taskList = [$task1, $task2];

        $formData
            = [
                'username' => 'UserName',
                'email' => 'user@todolist.fr',
                'roles' => ['ROLE_USER'],
                'tasks' => $taskList,
            ];

        $form = $this->factory->create(UserType::class, $userToTest);

        $user = new User();
        $user->setUsername('UserName');
        $user->setEmail('user@todolist.fr');

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $formDataUser = $form->getData();
        $this->assertSame($user->getUsername(), $formDataUser->getUsername());
        $this->assertSame($user->getEmail(), $formDataUser->getEmail());
        $this->assertSame($user->getRoles(), $formDataUser->getRoles());

        $expectedTasks = $user->getTasks()->toArray();
        $actualTasks = $formDataUser->getTasks()->toArray();

        $this->assertCount(count($expectedTasks), $actualTasks);

        foreach ($expectedTasks as $key => $expectedTask) {
            $this->assertSame($expectedTask->getTitle(), $actualTasks[$key]->getTitle());
            $this->assertSame($expectedTask->getContent(), $actualTasks[$key]->getContent());
        }
    }

    /**
     * Add an extension to validate data.
     *
     * @return ValidatorExtension
     */
    protected function getExtensions()
    {
        return [new ValidatorExtension(Validation::createValidator())];
    }
}
