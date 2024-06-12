<?php

namespace App\Manager;

use App\Entity\Task;
use App\Entity\User;
use App\EnumTodo\TaskStatus;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TaskRepository;


class TaskManager
{

    private readonly TaskRepository $taskRepository;


    /**
     * Construct EntityManagerInterface.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->taskRepository = $entityManager->getRepository(Task::class);
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
