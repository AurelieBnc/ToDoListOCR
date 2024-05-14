<?php

namespace App\Manager;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TaskRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class TaskManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TaskRepository $taskRepository,
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    public function createTask(Request $request, User $user): FormInterface
    {
        $task = new Task();
        $form = $this->formFactory->create(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->addTask($task);
            $this->taskRepository->add($task, true);
        }

        return $form;
    }

    public function editTask(Request $request, Task $task): FormInterface
    {
        $form = $this->formFactory->create(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskRepository->update($task, true);
        }

        return $form;
    }

    public function deleteTask(Task $task): void
    {
        $this->taskRepository->remove($task, true);
    }
}