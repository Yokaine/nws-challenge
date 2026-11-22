<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\ScoreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LeaderboardController extends AbstractController
{
    #[Route('/leaderboard', name: 'leaderboard')]
    public function index(
        GameRepository $gameRepository,
        ScoreRepository $scoreRepository
    ): Response {
        $game = $gameRepository->findOneBy([
            'slug' => 'puzzle'
        ]);

        $scores = $scoreRepository->findBy(
            ['game' => $game],
            ['score' => 'ASC']
        );

        return $this->render('leaderboard/index.html.twig', [
            'scores' => $scores,
        ]);
    }
}