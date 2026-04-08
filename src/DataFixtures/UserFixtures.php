<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setPassword($this->passwordEncoder->hashPassword(
            $user,
            "rootroot"
        ));
        $user->setEmail("rmasson.pro@gmail.com")
           ->setLastname("masson")
           ->setFirstname("rudy")
           ->setRoles(["ROLE_USER","ROLE_ADMIN"]);
        $manager->persist($user);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['userGroup'];
    }
}
