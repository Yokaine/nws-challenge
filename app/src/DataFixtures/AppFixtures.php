<?php

namespace App\DataFixtures;

use App\Entity\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $puzzle = new Game();

        $puzzle->setName('Puzzle');
        $puzzle->setSlug('puzzle');
        $puzzle->setDescription(
            'Remets les chiffres dans le bon ordre en utilisant le moins de coups possible.'
        );
        $puzzle->setCreatedAt(new \DateTime());

        $manager->persist($puzzle);

        $manager->flush();
    }
}