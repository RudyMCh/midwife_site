<?php

namespace App\DataFixtures;

use App\Entity\HomePage;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HomePageFixtures extends Fixture implements FixtureGroupInterface
{

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $homepage = new HomePage();
        $homepage
            ->setTitle('Cabinet GPT')
            ->setCatchphrase('test')
            ->setAbout('une phrase à propos du cabinet')
            ;
        $manager->persist($homepage);

        $manager->flush();
    }
    #[\Override]
    public static function getGroups(): array
    {
        return ['homepageGroup'];
    }

}
