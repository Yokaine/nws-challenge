<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminGameController extends AbstractController
{
    #[Route('/admin/game', name: 'app_admin_game')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findAll();

        return $this->render('admin_game/index.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/admin/game/add', name: 'app_admin_game_add', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function add(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $name = trim($request->request->get('name', ''));
        $slug = trim($request->request->get('slug', ''));
        $description = trim($request->request->get('description', ''));

        if ($name === '' || $slug === '') {
            $this->addFlash(
                'error',
                'Le nom et le slug sont obligatoires.'
            );

            return $this->redirectToRoute('app_admin_game');
        }

        $game = new Game();

        $game->setName($name);
        $game->setSlug($slug);
        $game->setDescription($description);
        $game->setCreatedAt(new \DateTime());

        $entityManager->persist($game);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Jeu ajouté avec succès.'
        );

        return $this->redirectToRoute('app_admin_game');
    }

    #[Route(
        '/admin/game/{id}/delete',
        name: 'app_admin_game_delete',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Game $game,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$game->getScores()->isEmpty()) {
            $this->addFlash(
                'error',
            'Impossible de supprimer ce jeu car des scores lui sont associés.'
            );

            return $this->redirectToRoute('app_admin_game');
        }

        $entityManager->remove($game);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Jeu supprimé avec succès.'
        );

        return $this->redirectToRoute('app_admin_game');
    }
}