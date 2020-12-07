<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Course;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $course = $manager->getRepository(Course::class)->findAll();
        $author = $manager->getRepository(User::class)->findAll();

        for ($i = 1; $i <=20 ; $i++){
            $comment = new Comment();
            $comment->setAuthor($author[$faker->numberBetween(0,count($author)-1)])
                ->setCourse($course[$faker->numberBetween(0,count($course)-1)])
                ->setCreatedAt($faker->dateTimeBetween('-15 days','now'))
                ->setContent($faker->paragraphs(2,true))
                ->setRating($faker->numberBetween(1,5));

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CourseFixtures::class
        ];
    }
}
