<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\Voter\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/user/list', name: 'app_user_list')]
    public function userList(UserRepository $userRepository): Response
    {
        if (!$this->isGranted(UserVoter::LIST)) {
            $this->addFlash('error', 'You do not have permission to list the users.');
            return $this->redirectToRoute('home');
        }

        $users = $userRepository->findAll();

        return $this->render('user/list.html.twig', [
            "users" => $users,
        ]);
    }

    #[Route('/user/create', name: 'app_user_create')]
    public function userCreate(Request $request): Response
    {
        if (!$this->isGranted(UserVoter::ADD)) {
            $this->addFlash('error', 'You do not have permission to create a user.');
            return $this->redirectToRoute('app_user_list');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'User successfully created.');
            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/edit/{id}', name: 'app_user_edit')]
    public function userEdit(User $user, Request $request): Response
    {
        if (!$this->isGranted(UserVoter::EDIT, $user)) {
            $this->addFlash('error', 'You do not have permission to edit this user.');
            return $this->redirectToRoute('app_user_list');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            
            $this->addFlash('success', 'User successfully updated.');
            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/delete/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function userDelete(User $user): Response
    {
        if (!$this->isGranted(UserVoter::DELETE, $user)) {
            $this->addFlash('error', 'You do not have permission to delete this user.');
            return $this->redirectToRoute('app_user_list');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'User successfully deleted.');
        return $this->redirectToRoute('app_user_list');
    }
}
