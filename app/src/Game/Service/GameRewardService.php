<?php

namespace App\Game\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class GameRewardService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function gameCompleted(User $user): void
    {
        $user->addExperience(30);
    }

    public function gameWon(User $user): void
    {
        $user->addExperience(100);
        $user->addTokens(10);
    }

    public function save(): void
    {
        $this->entityManager->flush();
    }
}