<?php

namespace App\DataFixtures;

use App\Entity\CourseCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CourseCategoryFixtures extends Fixture
{
    private $categories = ['Artisanat', 'Bien être', 'Paramédical', 'Langues', 'Technique', 'Informatique', 'Pédagogique'];

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        foreach($this->categories as $category) {
            $cat = new CourseCategory();
            $cat->setName($category);
            $cat->setDescription($faker->sentence);
            $manager->persist($cat);
        }

        $manager->flush();
    }
}
