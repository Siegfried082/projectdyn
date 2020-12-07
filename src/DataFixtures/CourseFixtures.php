<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\CourseCategory;
use App\Entity\CourseLevel;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CourseFixtures extends Fixture implements DependentFixtureInterface
{
    private $prices = [50, 100, 120, 180, 240.5, 300, 400];

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $slugify = new Slugify();
        $categories = $manager->getRepository(CourseCategory::class)->findAll();
        $levels = $manager->getRepository(CourseLevel::class)->findAll();
        for($i = 1; $i <= 25; $i++) {
            $course = new Course();
            $course->setCategory($categories[$faker->numberBetween(0, count($categories) -1)]);
            $course->setLevel($levels[$faker->numberBetween(0, count($levels) -1)]);
            $course->setName($faker->sentence(2, true));
            $course->setDuration($faker->numberBetween(1, 4));
            $course->setIsPublished($faker->boolean(90));
            $course->setSmallDescription($faker->paragraphs(1, true));
            $course->setFullDescription($faker->paragraphs(5, true));
            $course->setSlug($slugify->slugify($course->getName()));
            $course->setPrice($this->prices[$faker->numberBetween(0, count($this->prices) -1)]);
            $course->setCreatedAt($faker->dateTimeThisYear('now'));
            $course->setImage($i.'.jpg');
            $course->setProgram($i.'.pdf');
            $course->setSchedule($faker->dayOfWeek);
            $manager->persist($course);
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
            CourseCategoryFixtures::class,
            CourseLevelFixtures::class
        ];
    }
}
