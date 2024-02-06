<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Movie;
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
            $manager->flush();
            $categories[] = $category;

            $this->addReference('categ_' . $i, $category);
        }



    }
}
