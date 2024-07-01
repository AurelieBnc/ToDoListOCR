<?php

namespace App\Manager;

use App\Entity\Task;
use App\Entity\User;
use App\EnumTodo\TaskStatus;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskManager
{

    private readonly TaskRepository $taskRepository;

    /**
     * Construct EntityManagerInterface.
     *
     * @param EntityManagerInterface $entityManager manager for task
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->taskRepository = $entityManager->getRepository(Task::class);

    }

    /**
     * Function to create a task with owner.
     *
     * @param Task $task to create
     * @param User $user owner's task
     * @return Task
     */
    public function createTask(Task $task, User $user): Task
    {
        $task->setOwner($user);
        $this->taskRepository->add($task, true);

        return $task;
    }

    /**
     * Function to edit a task.
     *
     * @param Task $task to edit
     * @return Task
     */
    public function editTask(Task $task): Task
    {
        $this->taskRepository->update($task, true);

        return $task;
    }

    /**
     * Function to delete a task.
     *
     * @param Task $task to delete
     * @return void
     */
    public function deleteTask(Task $task): void
    {
        $this->taskRepository->remove($task, true);
    }

    /**
     * Function to change status of the task.
     *
     * @param Task $task to create
     * @return Task
     */
    public function toggle(Task $task): Task
    {
        $newStatus = $task->toggle($task->getStatus());
        $this->taskRepository->update($newStatus);

        return $task;
    }

    /**
     * Function to convert statusTask of Enum to String.
     *
     * @param TaskStatus $status enum needs to convert
     * @return string
     */
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
