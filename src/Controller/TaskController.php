<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Manager\TaskManager;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tasks', name: 'tasks')]
class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskManager $taskManager,
        private readonly TaskRepository $taskRepository,
    ) {
    }

    #[Route('/list-is-done', name: '_list_is_done')]
    #[IsGranted('TASK_LIST')]
    public function listIsDoneAction(Request $request): Response
    {
        $taskListPaginated = null;
        $page = $request->query->getInt('page', 1);
        $taskListPaginated = $this->taskRepository->findTasksListIsDonePaginated($page);

        $pages = $taskListPaginated['pages'] ?? null;
        if ( $page < 1  || $pages === null) {
            throw $this->createNotFoundException('Numéro de page invalide');
        }

        return $this->render('task/list.html.twig', [
            'tasks' => $taskListPaginated,
            'flag' => 1
        ]);
    }

    #[Route('/list-is-not-done', name: '_list_is_not_done')]
    #[IsGranted('TASK_LIST')]
    public function listIsNotDoneAction(Request $request): Response
    {
        $taskListPaginated = null;
        $page = $request->query->getInt('page', 1);
        $taskListPaginated = $this->taskRepository->findTasksListIsNotDonePaginated($page);

        $pages = $taskListPaginated['pages'] ?? null;
        if ( $page < 1  || $pages === null) {
            throw $this->createNotFoundException('Numéro de page invalide');
        }

        return $this->render('task/list.html.twig', [
            'tasks' => $taskListPaginated,
            'flag' => 0
        ]);
    }

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
            $data = $request->request->all();
            $isDone = $data['task']['isDone'] ?? null;

            if (isset($isDone)) {
                return $this->redirectToRoute('tasks_list_is_done');
            }
            
            return $this->redirectToRoute('tasks_list_is_not_done');
        }

        return $this->render('task/create_task.html.twig', [
            'taskForm' => $form,
        ]);
    }

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
            if ($task->getIsDone()) {
                return $this->redirectToRoute('tasks_list_is_done');
            }

            return $this->redirectToRoute('tasks_list_is_not_done');
        }

        return $this->render('task/edit_task.html.twig', [
            'taskForm' => $form,
            'task' => $task,
        ]);
    }

    #[Route(path: '/{id}/delete', name: '_delete')]
    #[IsGranted('TASK_DELETE', 'task')]
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
    #[IsGranted('TASK_TOGGLE', 'task')]
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
