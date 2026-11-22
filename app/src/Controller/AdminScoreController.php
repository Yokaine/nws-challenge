<?php

namespace App\Controller;

use App\Entity\Score;
use App\Repository\ScoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminScoreController extends AbstractController
{
    #[Route('/admin/score', name: 'app_admin_score')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(
        ScoreRepository $scoreRepository
    ): Response {
        $scores = $scoreRepository->findBy(
            [],
            ['score' => 'ASC']
        );

        return $this->render('admin_score/index.html.twig', [
            'scores' => $scores,
        ]);
    }

    #[Route(
        '/admin/score/{id}/edit',
        name: 'app_admin_score_edit',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Score $score,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $newScore = $request->request->getInt('score');

        if ($newScore < 1) {
            $this->addFlash(
                'error',
                'Le score doit être supérieur à 0.'
            );

            return $this->redirectToRoute('app_admin_score');
        }

        $score->setScore($newScore);

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Score modifié avec succès.'
        );

        return $this->redirectToRoute('app_admin_score');
    }

    #[Route(
        '/admin/score/{id}/delete',
        name: 'app_admin_score_delete',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Score $score,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->remove($score);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Score supprimé avec succès.'
        );

        return $this->redirectToRoute('app_admin_score');
    }
}