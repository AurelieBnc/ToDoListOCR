<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/users', name: 'users')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route('/list', name: '_list')]
    public function getUserList(Request $request): Response 
    {
        $userListPaginated = null;
        $page = $request->query->getInt('page', 1);
        $userListPaginated = $this->userRepository->findUsersListPaginated($page);

        $pages = $userListPaginated['pages'] ?? null;
        if ( $page < 1  || $pages === null) {
            throw $this->createNotFoundException('Numéro de page invalide');
        }

        return $this->render('user/user_list.html.twig', [
            'users' => $userListPaginated,
        ]);
    }
    
    #[Route('/create', name: '_create')]
    public function createAction(Request $request): RedirectResponse|Response
    {
        $userForm = $this->userManager->createUser($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $this->addFlash('success', 'L\'utilisateur a bien été ajouté !');

            return $this->redirectToRoute('users_list');
        }

        return $this->render('user/create_user.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: '_edit')]
    public function editAction(User $user, Request $request): RedirectResponse|Response
    {
        $userForm = $this->userManager->editUser($request, $user);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $this->userRepository->update($user, true);

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('users_list');
        }

        return $this->render('user/edit_user.html.twig', ['userForm' => $userForm->createView(), 'user' => $user]);
    }

    #[Route(path: '/{id}/delete', name: '_delete')]
    public function deleteTaskAction(User $user, Request $request): RedirectResponse
    {
        $this->userManager->deleteUser($user);

        $this->addFlash('success', 'L\'utilisateur a bien été supprimé !');

        return $this->redirectToRoute('users_list');
    }
}
