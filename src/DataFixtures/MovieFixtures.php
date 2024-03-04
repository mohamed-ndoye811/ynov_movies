<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 30; $i++) {
            $movie = new Movie();

            $movie->setName($faker->realText($faker->numberBetween(10, 128)));
            $movie->setDescription($faker->realText($faker->numberBetween(128, 4096)));
            $movie->setRate($faker->numberBetween(0, 5));
            $movie->setDuration($faker->numberBetween(1, 240));
            $manager->persist($movie);
        }

        $manager->flush();
    }
}
