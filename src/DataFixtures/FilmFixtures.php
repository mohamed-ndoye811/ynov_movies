<?php

namespace App\DataFixtures;

use App\Entity\Film;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FilmFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 30; $i++) {
            $film = new Film();

            $film->setNom($faker->realText($faker->numberBetween(10, 50)));
            $film->setDescription($faker->realText($faker->numberBetween(128, 256)));
            $film->setNote($faker->numberBetween(0, 5));
            $film->setDateDeParution($faker->dateTime());
            $film->addCategory($this->getReference('categ-' . $faker->numberBetween(0, 11)));
            $manager->persist($film);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class
        ];
    }
}
