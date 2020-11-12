<?php

namespace App\DataFixtures;

use App\Entity\CourseLevel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CourseLevelFixtures extends Fixture
{
    private $levels = [
        'CESI' => 'Certificat de base',
        'CESS' => 'CESI ou test d\'admission',
        'BES'  => 'CESS ou test d\'admission',
        'CES'  => 'CESS ou test d\'admission'
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->levels as $title => $prerequisite) {
            $level = new CourseLevel();
            $level->setName($title)
                  ->setPrerequisite($prerequisite);
            $manager->persist($level);
        }
        $manager->flush();
    }
}
