<?php

namespace App\DataFixtures;

use App\Entity\HomePage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class HomePageFixtures extends Fixture implements FixtureGroupInterface
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $homepage = new HomePage();
        $homepage
            ->setTitle('Cabinet Albandea-Gauthier')
            ->setCatchphrase('Votre sage-femme à Chevigny-Saint-Sauveur — grossesse, gynécologie et périnatalité')
            ->setAbout(
                'Le cabinet Albandea-Gauthier regroupe deux sages-femmes libérales à Chevigny-Saint-Sauveur, '
                . 'à 10 minutes de Dijon. Marie Albandea et Chloé Gauthier vous accompagnent à chaque étape '
                . 'de votre vie reproductive : suivi de grossesse, préparation à la naissance, naissance, '
                . 'rééducation périnéale, soutien à l\'allaitement et suivi gynécologique de prévention. '
                . 'Prises en charge 100 % Sécurité Sociale sans dépassement d\'honoraires.'
            )
            ->setMetaTitle('Cabinet Albandea-Gauthier — Sages-Femmes à Dijon')
            ->setMetaDescription(
                'Cabinet de sages-femmes libérales à Chevigny-Saint-Sauveur (21). '
                . 'Suivi de grossesse, préparation à la naissance, gynécologie et rééducation périnéale. '
                . 'Prise en charge Sécurité Sociale.'
            )
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
