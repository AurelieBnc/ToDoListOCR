<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

#[Route('/users', name: 'users')]
class UserController extends AbstractController
{
    #[Route('/list', name: 'users_list')]
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
    
}
