<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminUserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_admin_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin_user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/{id}/toggle-ban', name: 'app_admin_user_toggle_ban', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function toggleBan(
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        $user->setBanned(!$user->isBanned());

        $entityManager->flush();

        return $this->redirectToRoute('app_admin_user');
    }
}