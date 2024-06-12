<?php

namespace tests\AppBundle\Form;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use App\EnumTodo\TaskStatus;

/**
 * Unit testUnit test of the TaskType form.
 */
class TaskTypeTest extends TypeTestCase
{
    /**
     * Test valid data.
     */
    public function testSubmitValidData(): void
    {
        $taskToTest = new Task();

        $formData = [
            'title' => 'A title',
            'content' => 'A great content!',
            'status' => TaskStatus::IsDone
        ];

        $form = $this->factory->create(TaskType::class, $taskToTest);

        // Create new Task
        $task = new Task();
        $task->setTitle('A title');
        $task->setContent('A great content!');
        $task->setStatus(TaskStatus::IsDone);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * Add an extension to validate data.
     */
    protected function getExtensions()
    {
        return [new ValidatorExtension(Validation::createValidator())];

    }

}
