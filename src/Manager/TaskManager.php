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

    public function createTask(Task $task, User $user): Task
    {
        $task->setOwner($user);
        $this->taskRepository->add($task, true);

        return $task;
    }

    public function editTask(Task $task): Task
    {
        $this->taskRepository->update($task, true);

        return $task;
    }

    public function deleteTask(Task $task): void
    {
        $this->taskRepository->remove($task, true);
    }

    public function toggle(Task $task): Task
    {
        $task->toggle(!$task->getIsDone());
        $this->taskRepository->update($task, true);

        return $task;
    }
}
