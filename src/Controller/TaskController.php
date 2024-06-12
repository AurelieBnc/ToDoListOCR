<?php

namespace App\Controller;

use App\Entity\Task;
use App\EnumTodo\TaskStatus;
use App\Form\TaskType;
use App\Manager\TaskManager;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


/**
 * Provides functionality for managing, updating, deleting and marking tasks as complete within the system
 */
#[Route('/tasks', name: 'tasks')]
class TaskController extends AbstractController
{

    private readonly TaskManager $taskManager;
    private readonly TaskRepository $taskRepository;


    public function __construct(EntityManagerInterface $entityManager, TaskManager $taskManager)
    {
        $this->taskManager = $taskManager;
        $this->taskRepository = $entityManager->getRepository(Task::class);

    }

    /**
     * Paginated list of task by status
     * 
     * @param TaskStatus $status The status of task is an enum
     * 
     * @return Response
     */
    #[Route('/_list/{status}/{page}', name: '_list')]
    #[IsGranted('TASK_LIST')]
    public function listAction(TaskStatus $status, int $page): Response
    {
        $taskListPaginated = null;
        $taskListPaginated = $this->taskRepository->findByPagination($page, $status);

        ($pages = $taskListPaginated['pages'] ?? null);
        if ($page < 1  || $pages === null) {
            throw $this->createNotFoundException('Numéro de page invalide');
        }

        return $this->render('task/list.html.twig',
            [
                'status' => $status,
                'tasks' => $taskListPaginated,
            ]
        );

    }

    /**
     * Create task function
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    #[Route('/create', name: '_create')]
    #[IsGranted('TASK_CREATE')]
    public function createAction(Request $request): RedirectResponse|Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $this->taskManager->createTask($task, $this->getUser());
            $this->addFlash(
                'success','La tâche a bien été ajoutée.'
            );
            $status = $this->taskManager->convertStatusTaskToString($task->getStatus());
            
            return $this->redirectToRoute('tasks_list', ['status' => $status, 'page' => 1]);

        }

        return $this->render('task/create_task.html.twig',
            [
                'taskForm' => $form,
            ]
        );

    }

    /**
     * Edit task function
     * @param Request $request
     * @param Task $task Task we need update
     *
     * @return RedirectResponse|Response
     */
    #[Route('/{id}/edit', name: '_edit')]
    #[IsGranted('TASK_EDIT', 'task')]
    public function editAction(Task $task, Request $request): RedirectResponse|Response
    {        
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $this->taskManager->editTask($task);

            $this->addFlash(
                'success', 'La tâche a bien été mise à jour.'
            );
            $status = $this->taskManager->convertStatusTaskToString($task->getStatus());
            
            return $this->redirectToRoute('tasks_list', ['status' => $status, 'page' => 1]);

        }

        return $this->render('task/edit_task.html.twig',
            [
                'taskForm' => $form,
                'task' => $task,
            ]
        );

    }

    /**
     * Delete task function
     * 
     * @param Task $task Task we need delete
     * 
     * @return RedirectResponse
     */
    #[Route(path: '/{id}/delete', name: '_delete')]
    #[IsGranted('TASK_DELETE', 'task')]
    public function deleteTaskAction(Task $task): RedirectResponse
    {
        $this->taskManager->deleteTask($task);
        $status = $this->taskManager->convertStatusTaskToString($task->getStatus());

        $this->addFlash('success', 'La tâche a bien été supprimée !');

        return $this->redirectToRoute('tasks_list', ['status' => $status, 'page' => 1]);

    }

    /**
     * Toogle task function - change the status of task
     * 
     * @param Task $task Task we need change status
     * 
     * @return RedirectResponse
     */
    #[Route(path: '/{id}/toggle', name: '_toggle')]
    #[IsGranted('TASK_TOGGLE', 'task')]
    public function toggleTaskAction(Task $task): RedirectResponse
    {
        $task = $this->taskManager->toggle($task);
        $status = $this->taskManager->convertStatusTaskToString($task->getStatus());

        if ($status === 'isDone') {
            $this->addFlash(
                'success', 'La tâche a bien été marquée comme réalisée!'
            );
        }

        $this->addFlash(
            'success', 'La tâche a bien été marquée comme non faite!'
        );

        return $this->redirectToRoute('tasks_list', ['status' => $status, 'page' => 1]);

    }
}
