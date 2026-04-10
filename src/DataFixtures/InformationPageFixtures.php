<?php

namespace App\DataFixtures;

use App\Entity\InformationPage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class InformationPageFixtures extends Fixture implements FixtureGroupInterface
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $info = new InformationPage();
        $info
            ->setTitle('Informations utiles')
            ->setMetaTitle('Informations utiles — Cabinet Albandea-Gauthier')
            ->setMetaDescription(
                'Tarifs, accès, mentions légales et informations pratiques du cabinet de sages-femmes '
                .'Albandea-Gauthier à Chevigny-Saint-Sauveur (21).'
            )
            ->setLegal(
                '<h2>Mentions légales</h2>'
                .'<p><strong>Éditeur du site :</strong> Cabinet Albandea-Gauthier — Marie Albandea et Chloé Gauthier, '
                .'sages-femmes libérales en exercice conjoint.</p>'
                .'<p><strong>Adresse :</strong> 8 Rue des Saunières, 21800 Chevigny-Saint-Sauveur</p>'
                .'<p><strong>Téléphone :</strong> 03 80 45 32 12</p>'
                .'<h3>Marie Albandea</h3>'
                .'<ul>'
                .'<li>N° RPPS : 20202020938</li>'
                .'<li>N° ADELI : 987654321</li>'
                .'<li>N° Ordinal : 69-001428</li>'
                .'<li>SIRET : 822 837 456 00020</li>'
                .'<li>RCP : MACSF — contrat n° MACSF-2014-03177</li>'
                .'</ul>'
                .'<h3>Chloé Gauthier</h3>'
                .'<ul>'
                .'<li>N° RPPS : 10101010949</li>'
                .'<li>N° ADELI : 123456789</li>'
                .'<li>N° Ordinal : 21-000351</li>'
                .'<li>SIRET : 822 837 456 00012</li>'
                .'<li>RCP : MACSF — contrat n° MACSF-2015-04821</li>'
                .'</ul>'
                .'<p>Ordre des Sages-Femmes de Côte-d\'Or — <a href="https://www.ordre-sages-femmes.fr" target="_blank">www.ordre-sages-femmes.fr</a></p>'
                .'<h2>Hébergement</h2>'
                .'<p>Ce site est hébergé par o2switch, 222-224 Boulevard Gustave Flaubert, 63000 Clermont-Ferrand.</p>'
                .'<h2>Propriété intellectuelle</h2>'
                .'<p>L\'ensemble du contenu de ce site (textes, photographies, illustrations) est protégé par le droit d\'auteur. '
                .'Toute reproduction sans autorisation expresse est interdite.</p>'
                .'<h2>Données personnelles (RGPD)</h2>'
                .'<p>Ce site ne collecte aucune donnée personnelle via formulaire. '
                .'Un outil de mesure d\'audience anonymisé (Matomo en mode cookieless) est utilisé pour améliorer le service. '
                .'Conformément au RGPD, vous pouvez exercer vos droits d\'accès et de suppression en nous contactant par téléphone ou courrier.</p>'
            )
            ->setPrice(
                '<h2>Tarifs</h2>'
                .'<p>L\'ensemble de nos consultations sont remboursées par l\'Assurance Maladie '
                .'<strong>sans dépassement d\'honoraires</strong> (secteur 1).</p>'
                .'<h3>Consultations courantes</h3>'
                .'<ul>'
                .'<li>Consultation de suivi de grossesse : <strong>28,00 €</strong> (remboursé SS)</li>'
                .'<li>Consultation gynécologique : <strong>28,00 €</strong> (remboursé SS)</li>'
                .'<li>Séance de préparation à la naissance : <strong>36,40 €</strong> (remboursé SS, 8 séances)</li>'
                .'<li>Séance de rééducation périnéale : <strong>28,00 €</strong> (remboursé SS, 10 séances)</li>'
                .'<li>Monitoring à domicile : <strong>48,00 €</strong> (remboursé SS)</li>'
                .'</ul>'
                .'<p>Mutuelle : selon votre contrat, le ticket modérateur peut être pris en charge. '
                .'Rapprochez-vous de votre organisme complémentaire.</p>'
                .'<p><em>Tarifs en vigueur au 1er janvier 2026 — sous réserve de revalorisation conventionnelle.</em></p>'
            )
            ->setComing(
                '<h2>Nouveautés à venir</h2>'
                .'<p>Nous travaillons actuellement à l\'ouverture d\'un groupe de préparation à la naissance '
                .'spécialement dédié aux grossesses gémellaires. Si vous êtes concernée, '
                .'n\'hésitez pas à vous préinscrire par téléphone.</p>'
                .'<p>Des séances d\'ostéopathie en cabinet seront également proposées prochainement '
                .'en partenariat avec un ostéopathe D.O. spécialisé périnatalité.</p>'
            )
            ->setLinks(
                '<h2>Liens utiles</h2>'
                .'<ul>'
                .'<li><a href="https://www.ameli.fr" target="_blank">Ameli.fr</a> — Assurance Maladie</li>'
                .'<li><a href="https://www.ordre-sages-femmes.fr" target="_blank">Ordre National des Sages-Femmes</a></li>'
                .'<li><a href="https://www.cnsf.fr" target="_blank">Collège National des Sages-Femmes de France</a></li>'
                .'<li><a href="https://www.chu-dijon.fr/maternite" target="_blank">Maternité du CHU Dijon-Bourgogne</a></li>'
                .'<li><a href="https://www.doctolib.fr" target="_blank">Doctolib</a> — prise de rendez-vous en ligne</li>'
                .'<li><a href="https://www.1000-premiers-jours.fr" target="_blank">1000 premiers jours</a> — ressources parentalité</li>'
                .'</ul>'
            )
            ->setMention('Site réalisé par Rudy Masson — 2026')
        ;
        $manager->persist($info);

        $manager->flush();
    }

    #[\Override]
    public static function getGroups(): array
    {
        return ['informationGroup'];
    }
}
