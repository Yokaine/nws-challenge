<?php

namespace App\Game\Controller;

use App\Entity\Game;
use App\Entity\Score;
use App\Game\Service\GameRewardService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GameController extends AbstractController
{
    #[Route('/game/puzzle', name: 'game_puzzle')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();

        $gameToken = bin2hex(random_bytes(32));

        $session->set('puzzle_game_token', $gameToken);
        $session->set('puzzle_completed', false);

        return $this->render('game/puzzle.html.twig', [
            'gameToken' => $gameToken,
        ]);
    }

    #[Route('/game/puzzle/score', name: 'game_puzzle_score', methods: ['POST'])]
    public function saveScore(
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security,
        GameRewardService $rewardService,
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Vous devez être connecté.'
            ], 401);
        }

        $session = $request->getSession();

        if ($session->get('puzzle_completed', false)) {
            return $this->json([
                'success' => false,
                'message' => 'Cette partie a déjà été enregistrée.'
            ], 400);
        }

        $data = json_decode($request->getContent(), true);

        $moves = $data['moves'] ?? null;
        $gameToken = $data['gameToken'] ?? null;

        $sessionGameToken = $session->get('puzzle_game_token');

        if (
            !is_string($gameToken) ||
            !is_string($sessionGameToken) ||
            !hash_equals($sessionGameToken, $gameToken)
        ) {
            return $this->json([
                'success' => false,
                'message' => 'Partie invalide.'
            ], 400);
        }

        if (!is_int($moves) || $moves < 1) {
            return $this->json([
                'success' => false,
                'message' => 'Score invalide.'
            ], 400);
        }

        $game = $entityManager
            ->getRepository(Game::class)
            ->findOneBy(['slug' => 'puzzle']);

        if (!$game) {
            return $this->json([
                'success' => false,
                'message' => 'Jeu introuvable.'
            ], 404);
        }

        $score = new Score();
        $score->setScore($moves);
        $score->setPlayer($user);
        $score->setGame($game);
        $score->setCreatedAt(new \DateTime());

        $entityManager->persist($score);

        $rewardService->gameCompleted($user);
        $rewardService->gameWon($user);

        $rewardService->save();

        $session->set('puzzle_completed', true);

        return $this->json([
            'success' => true,
            'message' => 'Score enregistré.'
        ]);
    }
}