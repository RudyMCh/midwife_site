<?php

namespace App\DataFixtures;

use App\Entity\Degree;
use App\Entity\Midwife;
use App\Entity\Path;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MidwifeFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        // ── Chloé Gauthier ──────────────────────────────────────────────────────
        $chloe = new Midwife()
            ->setLastname('Gauthier')
            ->setFirstname('Chloé')
            ->setPhone('+33646429076')
            ->setEmail('chloe21.gauthier@yahoo.fr')
            ->setDoctolibUrl('https://www.doctolib.fr/sage-femme/chevigny-saint-sauveur/chloe-gauthier')
            ->setBackgroundColor1('#e8a0bf')
            ->setAboutMe(
                'Sage-femme libérale depuis 2015, j\'exerce au cabinet Albandea-Gauthier à Chevigny-Saint-Sauveur, '
                .'à quelques minutes de Dijon. Mon accompagnement s\'inscrit dans une approche globale et bienveillante : '
                .'je vous suis de la déclaration de grossesse jusqu\'aux premières semaines de vie de votre bébé, '
                .'en passant par la rééducation périnéale et le suivi gynécologique de prévention.'
            )
            ->setDescription(
                'Diplômée en 2015 de l\'École de Sages-Femmes de Dijon, j\'ai débuté ma carrière en maternité avant '
                .'d\'ouvrir mon cabinet libéral à Chevigny-Saint-Sauveur en 2016 avec ma consœur Marie Albandea. '
                .'Ma pratique est centrée sur l\'écoute, la disponibilité et le respect de vos choix. '
                ."\n\n"
                .'Je prends en charge le suivi de grossesse (du premier trimestre à l\'accouchement), '
                .'la préparation à la naissance (méthode classique, haptonomie, yoga prénatal), '
                .'la surveillance du nouveau-né à domicile et la rééducation périnéale après l\'accouchement. '
                .'Je réalise également le suivi gynécologique de la femme tout au long de sa vie : '
                .'contraception, dépistage (frottis cervico-utérin), consultation de ménopause. '
                ."\n\n"
                .'Formée à l\'acupuncture obstétricale, je propose des séances de déclenchement naturel du travail '
                .'et de soulagement des inconforts de fin de grossesse (lombalgie, nausées, version par siège). '
                .'N\'hésitez pas à prendre rendez-vous via Doctolib ou à m\'appeler directement.'
            )
            ->setMetaTitle('Chloé Gauthier — Sage-femme à Chevigny-Saint-Sauveur')
            ->setMetaDescription(
                'Chloé Gauthier, sage-femme libérale à Chevigny-Saint-Sauveur (21). '
                .'Suivi de grossesse, gynécologie, préparation à la naissance et rééducation périnéale.'
            )
            ->setRpps('10101010949')
            ->setAdeli('123456789')
            ->setRcpLibelle('MACSF — Responsabilité Civile Professionnelle')
            ->setRcpNumeroContrat('MACSF-2015-04821')
            ->setNumeroOrdinal('21-000351')
            ->setSiret('82283745600012')
        ;

        $manager->persist($chloe);

        // Parcours professionnel — Chloé
        foreach ([
            ['title' => 'Sage-femme libérale — Cabinet Albandea-Gauthier', 'start' => '2016', 'end' => null,   'city' => 'Chevigny-Saint-Sauveur'],
            ['title' => 'Sage-femme en maternité (niveau IIa)',              'start' => '2015', 'end' => '2016', 'city' => 'Dijon'],
        ] as $p) {
            $path = new Path()
                ->setTitle($p['title'])
                ->setStart($p['start'])
                ->setEnd($p['end'])
                ->setCity($p['city'])
                ->setMidwife($chloe)
            ;
            $manager->persist($path);
        }

        // Diplômes — Chloé
        foreach ([
            ['title' => 'Diplôme d\'État de Sage-Femme',           'establishment' => 'École de Sages-Femmes de Dijon',  'year' => '2015', 'type' => 'Diplôme d\'État'],
            ['title' => 'DU Acupuncture obstétricale et néonatale', 'establishment' => 'Université de Bourgogne — Dijon', 'year' => '2018', 'type' => 'Diplôme Universitaire'],
        ] as $d) {
            $degree = new Degree()
                ->setTitle($d['title'])
                ->setEstablishment($d['establishment'])
                ->setYear($d['year'])
                ->setType($d['type'])
                ->setMidwife($chloe)
            ;
            $manager->persist($degree);
        }

        // Services — Chloé
        foreach ([
            DomainFixtures::SERVICE_SUIVI_GROSSESSE,
            DomainFixtures::SERVICE_CONSULTATION_PRENATALE,
            DomainFixtures::SERVICE_MONITORING,
            DomainFixtures::SERVICE_SUIVI_GYNECO,
            DomainFixtures::SERVICE_CONTRACEPTION,
            DomainFixtures::SERVICE_FROTTIS,
            DomainFixtures::SERVICE_PREPARATION_CLASSIQUE,
            DomainFixtures::SERVICE_HAPTONOMIE,
            DomainFixtures::SERVICE_REEDUCATION_PERINEALE,
            DomainFixtures::SERVICE_CONSULTATION_POSTNATALE,
            DomainFixtures::SERVICE_ALLAITEMENT,
            DomainFixtures::SERVICE_EXAMEN_NOUVEAU_NE,
        ] as $ref) {
            /** @var Service $svc */
            $svc = $this->getReference($ref, Service::class);
            $svc->addMidwife($chloe);
        }

        // ── Marie Albandea ───────────────────────────────────────────────────────
        $marie = new Midwife()
            ->setLastname('Albandea')
            ->setFirstname('Marie')
            ->setPhone('0380453212')
            ->setEmail('marie.albandea@cabinet-albandea-gauthier.fr')
            ->setDoctolibUrl('https://www.doctolib.fr/sage-femme/chevigny-saint-sauveur/marie-albandea')
            ->setBackgroundColor1('#7bbfb5')
            ->setAboutMe(
                'Sage-femme libérale depuis 2014, je partage avec Chloé Gauthier le cabinet de Chevigny-Saint-Sauveur. '
                .'Passionnée par l\'accompagnement périnatal, je mets un point d\'honneur à vous offrir un suivi personnalisé, '
                .'dans un environnement rassurant et chaleureux, que ce soit pour votre grossesse, votre accouchement '
                .'ou votre retour à la maison.'
            )
            ->setDescription(
                'Diplômée en 2014 de l\'École de Sages-Femmes de Lyon, j\'ai exercé deux ans en maternité de niveau III '
                .'avant de rejoindre l\'exercice libéral. Depuis 2016, j\'exerce au cabinet Albandea-Gauthier '
                .'à Chevigny-Saint-Sauveur où je prends en charge l\'ensemble du suivi périnatal. '
                ."\n\n"
                .'Ma pratique inclut le suivi de grossesse physiologique et à risque, la préparation à la naissance '
                .'(méthode classique et yoga prénatal), les visites à domicile en post-partum, la rééducation périnéale '
                .'et le suivi gynécologique de prévention. '
                ."\n\n"
                .'Formée à l\'entretien prénatal précoce (EPP), je propose un espace de parole dès le premier trimestre '
                .'pour aborder vos attentes, vos craintes et préparer votre projet de naissance en toute sérénité. '
                .'Je suis également référente allaitement au sein du cabinet.'
            )
            ->setMetaTitle('Marie Albandea — Sage-femme à Chevigny-Saint-Sauveur')
            ->setMetaDescription(
                'Marie Albandea, sage-femme à Chevigny-Saint-Sauveur (21). '
                .'Grossesse, gynécologie, post-partum et rééducation périnéale. Prise en charge Sécurité Sociale.'
            )
            ->setRpps('20202020938')
            ->setAdeli('987654321')
            ->setRcpLibelle('MACSF — Responsabilité Civile Professionnelle')
            ->setRcpNumeroContrat('MACSF-2014-03177')
            ->setNumeroOrdinal('69-001428')
            ->setSiret('82283745600020')
        ;

        $manager->persist($marie);

        // Parcours professionnel — Marie
        foreach ([
            ['title' => 'Sage-femme libérale — Cabinet Albandea-Gauthier', 'start' => '2016', 'end' => null,   'city' => 'Chevigny-Saint-Sauveur'],
            ['title' => 'Sage-femme en maternité (niveau III)',              'start' => '2014', 'end' => '2016', 'city' => 'Lyon'],
        ] as $p) {
            $path = new Path()
                ->setTitle($p['title'])
                ->setStart($p['start'])
                ->setEnd($p['end'])
                ->setCity($p['city'])
                ->setMidwife($marie)
            ;
            $manager->persist($path);
        }

        // Diplômes — Marie
        foreach ([
            ['title' => 'Diplôme d\'État de Sage-Femme',                'establishment' => 'École de Sages-Femmes de Lyon',              'year' => '2014', 'type' => 'Diplôme d\'État'],
            ['title' => 'Capacité en Entretien Prénatal Précoce (EPP)', 'establishment' => 'Collège National des Sages-Femmes de France', 'year' => '2017', 'type' => 'Certificat de capacité'],
        ] as $d) {
            $degree = new Degree()
                ->setTitle($d['title'])
                ->setEstablishment($d['establishment'])
                ->setYear($d['year'])
                ->setType($d['type'])
                ->setMidwife($marie)
            ;
            $manager->persist($degree);
        }

        // Services — Marie
        foreach ([
            DomainFixtures::SERVICE_SUIVI_GROSSESSE,
            DomainFixtures::SERVICE_CONSULTATION_PRENATALE,
            DomainFixtures::SERVICE_MONITORING,
            DomainFixtures::SERVICE_SUIVI_GYNECO,
            DomainFixtures::SERVICE_CONTRACEPTION,
            DomainFixtures::SERVICE_PREPARATION_CLASSIQUE,
            DomainFixtures::SERVICE_YOGA_PRENATAL,
            DomainFixtures::SERVICE_REEDUCATION_PERINEALE,
            DomainFixtures::SERVICE_CONSULTATION_POSTNATALE,
            DomainFixtures::SERVICE_ALLAITEMENT,
            DomainFixtures::SERVICE_EXAMEN_NOUVEAU_NE,
            DomainFixtures::SERVICE_CONSULTATION_NOURRISSON,
        ] as $ref) {
            /** @var Service $svc */
            $svc = $this->getReference($ref, Service::class);
            $svc->addMidwife($marie);
        }

        $manager->flush();
    }

    #[\Override]
    public static function getGroups(): array
    {
        return ['midwifeGroup'];
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [DomainFixtures::class];
    }
}
