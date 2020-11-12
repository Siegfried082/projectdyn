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
       $users = $manager->getRepository(User::class)->findAll();
       $courses = $manager->getRepository(Course::class)->findAll();

       for($i = 1; $i <= 60; $i++) {
           $comment = new Comment();
           $comment->setCreatedAt($faker->dateTimeBetween('now'));
           $comment->setRating($faker->numberBetween(1, 5));
           $comment->setContent($faker->paragraph());
           $comment->setAuthor($users[$faker->numberBetween(0, count($users) -1)]);
           $comment->setCourse($courses[$faker->numberBetween(0, count($courses) -1)]);
           $manager->persist($comment);
       }
       $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return class-string[]
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CourseFixtures::class
        ];
    }
}
