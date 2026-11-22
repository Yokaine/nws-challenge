<?php

namespace App\Controller;

use App\Game\Service\MusicService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MusicController extends AbstractController
{
    #[Route('/music', name: 'music')]
    public function index(MusicService $musicService): Response
    {
        $tracks = $musicService->getChillTracks();

        return $this->render('music/index.html.twig', [
            'tracks' => $tracks,
        ]);
    }
}