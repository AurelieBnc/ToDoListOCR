<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users', name: 'users')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route('/list/{page}', name: '_list', defaults: ['page' => 1])]
    #[IsGranted('USER_LIST')]
    public function getUserList(int $page): Response 
    {
        $userListPaginated = null;
        $userListPaginated = $this->userRepository->findByPagination($page);

        return $this->render('user/user_list.html.twig', [
            'users' => $userListPaginated,
        ]);
    }
    
    #[Route('/create', name: '_create')]
    #[IsGranted('USER_CREATE')]
    public function createAction(Request $request): RedirectResponse|Response
    {
        $user = new User();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $plainPassword = $userForm->get('plainPassword')->getData();

            $user = $this->userManager->createUser($user, $plainPassword);
            $this->addFlash('success', 'L\'utilisateur a bien été ajouté !');

            return $this->redirectToRoute('users_list');
        }
        return $this->render('user/create_user.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: '_edit')]
    #[IsGranted('USER_EDIT', 'user')]
    public function editAction(User $user, Request $request): RedirectResponse|Response
    {
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $plainPassword = $userForm->get('plainPassword')->getData();
            $user = $this->userManager->editUser($user, $plainPassword);
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('users_list');
        }
        return $this->render('user/edit_user.html.twig', ['userForm' => $userForm->createView(), 'user' => $user]);
    }

    #[Route(path: '/{id}/delete', name: '_delete')]
    #[IsGranted('USER_DELETE', 'user')]
    public function deleteTaskAction(User $user): RedirectResponse
    {
        $this->userManager->deleteUser($user);
        $this->addFlash('success', 'L\'utilisateur a bien été supprimé !');

        return $this->redirectToRoute('users_list');
    }
}
