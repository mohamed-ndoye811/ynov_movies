<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Film;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $categories = [];
        for ($i = 0; $i < 12; $i++) {
            $category = new Category();

            $category->setName($faker->word());

            $manager->persist($category);
            $categories[] = $category;

            $this->addReference('categ_'. $i, $category);
        }

        for ($i = 0; $i < 30; $i++) {
            $film = new Film();

            $film->setNom($faker->realText($faker->numberBetween(10, 128)));
            $film->setDescription($faker->realText($faker->numberBetween(1024, 2048)));
            $film->setNote($faker->numberBetween(0, 5));
            $film->setDateDeParution($faker->dateTime());


            $numTags = $faker->numberBetween(1, 4);
            for ($j = 0; $j < $numTags; $j++) {
                $categoryReference = $this->getReference('categ_' . rand(0, 11));
                $film->addCategory($categoryReference);
            }


            $manager->persist($film);
        }

        $manager->flush();
    }
}
