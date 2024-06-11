<?php

namespace App\Manager;

use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskStatus;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TaskRepository;
use Symfony\Component\Form\FormFactoryInterface;

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
        $newStatus = $task->toggle($task->getStatus());
        $this->taskRepository->update($newStatus);

        return $task;
    }

    public function convertStatusTaskToString(TaskStatus $status): string
    {
        if (TaskStatus::IsDone === $status) {
            return 'isDone';
        }
        if (TaskStatus::Todo === $status) {
            return 'todo';
        }
    }
}
