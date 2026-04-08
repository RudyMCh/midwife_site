<?php

namespace App\DataFixtures;

use App\Entity\HomePage;
use App\Entity\Office;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OfficeFixtures extends Fixture implements FixtureGroupInterface
{

    public function load(ObjectManager $manager): void
    {
        $office = new Office();
        $office
            ->setName('Cabinet Pontonnier-Gauthier-Todesco')
            ->setAddress('5 rue Voltaire')
            ->setZipcode('21800')
            ->setCity('Quetigny')
        ;
        $manager->persist($office);

        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['officeGroup'];
    }

}
