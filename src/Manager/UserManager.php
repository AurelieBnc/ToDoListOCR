<?php

namespace App\Manager;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class UserManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly UserRepository $userRepository,
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    public function createUser(Request $request): FormInterface
    {
        $user = new User();
        $userForm = $this->formFactory->create(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $userForm->get('plainPassword')->getData()
            )
        );

            $this->userRepository->add($user, true);
        }

        return $userForm;
    }

    public function editUser(Request $request, User $user): FormInterface
    {
        $userForm = $this->formFactory->create(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $userForm->get('plainPassword')->getData()
                )
            );
            $this->userRepository->update($user, true);
        }

        return $userForm;
    }

    public function deleteUser(User $user): void
    {
        $this->userRepository->remove($user, true);
    }
}
