<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/users', name: 'users')]
class UserController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function getUserList(UserRepository $userRepository, Request $request): Response 
    {
        $userListPaginated = null;
        $page = $request->query->getInt('page', 1);
        $userListPaginated = $userRepository->findUsersListPaginated($page);

        $pages = $userListPaginated['pages'] ?? null;
        if ( $page < 1  || $pages === null) {
            throw $this->createNotFoundException('Numéro de page invalide');
        }

        return $this->render('user/user_list.html.twig', [
            'users' => $userListPaginated,
        ]);
    }
    
    #[Route('/create', name: '_create')]
    public function createUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User;
        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $userForm->get('plainPassword')->getData()
                )
            );
            $user = $userForm->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a bien été ajouté !');

            return $this->redirectToRoute('users_list');
        }

        return $this->render('user/create_user.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }
}
