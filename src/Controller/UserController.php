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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;


/**
 * Provides functionality for managing, updating, deleting and marking users as complete within the system
 */
#[Route('/users', name: 'users')]
class UserController extends AbstractController
{
    
    private readonly UserManager $userManager;

    private readonly UserRepository $userRepository;


    /**
     * Construct with entityManagerInterface and UserManager.
     */
    public function __construct(EntityManagerInterface $entityManager, UserManager $userManager)
    {
        $this->userManager = $userManager;
        $this->userRepository = $entityManager->getRepository(User::class);

    }

    /**
     * Paginated list of user.
     * 
     * @param int $page number of the page called
     * @return Response
     */
    #[Route('/list/{page}', name: '_list', defaults: ['page' => 1])]
    #[IsGranted('USER_LIST')]
    public function getUserList(int $page): Response
    {
        $userListPaginated = null;
        $userListPaginated = $this->userRepository->findByPagination($page);

        return $this->render('user/user_list.html.twig', ['users' => $userListPaginated]);
    }
    
    /**
     * Create user function.
     * 
     * @param Request $request request
     * @return RedirectResponse|Response
     */
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

        return $this->render('user/create_user.html.twig', ['userForm' => $userForm->createView()]);
    }

    /**
     * Edit user function.
     * 
     * @param User $user user to edit
     * @param Request $request request
     * @return RedirectResponse|Response
     */
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

    /**
     * Delete user function.
     * 
     * @param User $user user to delete
     * @return RedirectResponse|Response
     */
    #[Route(path: '/{id}/delete', name: '_delete')]
    #[IsGranted('USER_DELETE', 'user')]
    public function deleteTaskAction(User $user): RedirectResponse
    {
        $this->userManager->deleteUser($user);
        $this->addFlash('success', 'L\'utilisateur a bien été supprimé !');

        return $this->redirectToRoute('users_list');
    }

}
