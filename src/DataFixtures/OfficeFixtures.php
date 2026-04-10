<?php

namespace App\DataFixtures;

use App\Entity\Office;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class OfficeFixtures extends Fixture implements FixtureGroupInterface
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $office = new Office();
        $office
            ->setName('Cabinet Albandea-Gauthier')
            ->setAddress('8 Rue des Saunières')
            ->setZipcode('21800')
            ->setCity('Chevigny-Saint-Sauveur')
            ->setPhone('0380453212')
            ->setAbout(
                'Le cabinet Albandea-Gauthier est situé à Chevigny-Saint-Sauveur, à 10 minutes du centre de Dijon. '
                .'Il est accessible en voiture (parking gratuit devant le cabinet) et en transports en commun '
                .'(ligne de bus Divia, arrêt Mairie de Chevigny). '
                .'Nos locaux sont de plain-pied et accessibles aux personnes à mobilité réduite. '
                ."\n\n"
                .'Le cabinet est ouvert du lundi au vendredi de 8h à 19h et le samedi matin de 8h à 12h. '
                .'Des créneaux de consultations urgentes sont réservés chaque jour. '
                .'Pour toute urgence obstétricale en dehors des heures d\'ouverture, '
                .'contactez le 15 (SAMU) ou la maternité de votre secteur.'
            )
            ->setUrlGoogleMap('https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2713.7!2d5.1275!3d47.2867!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sCabinet+Albandea-Gauthier!5e0!3m2!1sfr!2sfr!4v1680000000000')
            ->setLatitude('47.2867')
            ->setLongitude('5.1275')
        ;
        $manager->persist($office);

        $manager->flush();
    }

    #[\Override]
    public static function getGroups(): array
    {
        return ['officeGroup'];
    }
}
