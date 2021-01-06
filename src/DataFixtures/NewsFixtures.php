<?php

namespace App\DataFixtures;

use App\Entity\News;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class NewsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $slugify = new Slugify();
        for ($i = 1; $i <=20 ; $i++){
            $news = new News();
            $news->setName($faker->name);
            $news->setCreatedAt($faker->dateTimeBetween('-15 days','now'));
            $news->setContent($faker->paragraphs(5, true));
            $news->setImage('0'.$i.'.png');
            $news->setSlug($slugify->slugify($news->getName()));

            $manager->persist($news);
        }

        $manager->flush();

    }
}
