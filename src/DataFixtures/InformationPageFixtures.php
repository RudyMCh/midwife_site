<?php

namespace App\DataFixtures;

use App\Entity\InformationPage;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class InformationPageFixtures extends Fixture implements FixtureGroupInterface
{

    public function load(ObjectManager $manager): void
    {
        $info = new InformationPage();
        $info
            ->setLegal('test')
        ;
        $manager->persist($info);

        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['informationGroup'];
    }

}
