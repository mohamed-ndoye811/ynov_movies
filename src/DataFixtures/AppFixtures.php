<?php

namespace App\DataFixtures;

use App\Entity\Film;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 30; $i++) {
            echo $faker->name() . "\n";
            $film = new Film();

            $film->setNom($faker->realText($faker->numberBetween(10, 128)));
            $film->setDescription($faker->realText($faker->numberBetween(1024, 2048)));
            $film->setNote($faker->numberBetween(0, 5));
            $film->setDateDeParution($faker->dateTime());

            $manager->persist($film);
            $manager->flush();
        }
    }
}
