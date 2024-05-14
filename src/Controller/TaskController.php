<?php

namespace App\Controller;

use App\Entity\Task;
use App\Manager\TaskManager;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tasks', name: 'tasks')]
class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskManager $taskManager,
        private readonly TaskRepository $taskRepository,
    ) {
    }

    #[Route('/list-is-done', name: '_list_is_done')]
    public function listIsDoneAction(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('task/list.html.twig', ['tasks' => $this->taskRepository->findTasksListIsDonePaginated($page)]);
    }

    #[Route('/list-is-not-done', name: '_list_is_not_done')]
    public function listIsNotDoneAction(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('task/list.html.twig', ['tasks' => $this->taskRepository->findTasksListIsNotDonePaginated($page)]);
    }

    #[Route('/create', name: '_create')]
    public function createAction(Request $request): RedirectResponse|Response
    {
        $taskForm = $this->taskManager->createTask($request, $this->getUser());

        if ($taskForm->isSubmitted() && $taskForm->isValid()) {
            $this->addFlash(
                'success','La tâche a bien été ajoutée.'
            );
            $isDone = $request->request->has('isDone') ? (bool)$request->request->get('isDone') : false;
            if ($isDone) {
                return $this->redirectToRoute('tasks_list_is_done');
            }
            return $this->redirectToRoute('tasks_list_is_not_done');
        }

        return $this->render('task/create_task.html.twig', [
            'taskForm' => $taskForm,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit')]
    public function editAction(Task $task, Request $request): RedirectResponse|Response
    {
        $taskForm = $this->taskManager->editTask($request, $task);

        if ($taskForm->isSubmitted() && $taskForm->isValid()) {
            $this->addFlash(
                'success', 'La tâche a bien été mise à jour.'
            );
            if ($task->getIsDone()) {
                return $this->redirectToRoute('tasks_list_is_done');
            }

            return $this->redirectToRoute('tasks_list_is_not_done');
        }

        return $this->render('task/edit_task.html.twig', [
            'taskForm' => $taskForm,
            'task' => $task,
        ]);
    }

    #[Route(path: '/{id}/delete', name: '_delete')]
    public function deleteTaskAction(Task $task, Request $request): RedirectResponse
    {
        $isDone = $task->getIsDone();
        $this->taskManager->deleteTask($task);

        $this->addFlash('success', 'La tâche a bien été supprimée !');

        if ($isDone) {
            return $this->redirectToRoute('tasks_list_is_done');
        }

        return $this->redirectToRoute('tasks_list_is_not_done');
    }

    #[Route(path: '/{id}/toggle', name: '_toggle')]
    public function toggleTaskAction(Task $task): RedirectResponse
    {
        $task = $this->taskManager->toggle($task);

        if ($task->getIsDone()) {
            $this->addFlash(
                'success', 'La tâche a bien été marquée comme réalisée!'
            );

            return $this->redirectToRoute('tasks_list_is_done');
        }

        $this->addFlash(
            'success', 'La tâche a bien été marquée comme non faite!'
        );

        return $this->redirectToRoute('tasks_list_is_not_done');
    }
}
