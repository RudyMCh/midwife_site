<?php

namespace App\DataFixtures;

use App\Entity\Domain;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class DomainFixtures extends Fixture implements FixtureGroupInterface
{
    // Domain references
    public const DOMAIN_GROSSESSE = 'domain-grossesse';
    public const DOMAIN_GYNECOLOGIE = 'domain-gynecologie';
    public const DOMAIN_PREPARATION = 'domain-preparation';
    public const DOMAIN_PERINATALITE = 'domain-perinatalite';
    public const DOMAIN_NOUVEAU_NE = 'domain-nouveau-ne';

    // Service references
    public const SERVICE_SUIVI_GROSSESSE = 'service-suivi-grossesse';
    public const SERVICE_CONSULTATION_PRENATALE = 'service-consultation-prenatale';
    public const SERVICE_MONITORING = 'service-monitoring';
    public const SERVICE_SUIVI_GYNECO = 'service-suivi-gyneco';
    public const SERVICE_CONTRACEPTION = 'service-contraception';
    public const SERVICE_FROTTIS = 'service-frottis';
    public const SERVICE_PREPARATION_CLASSIQUE = 'service-preparation-classique';
    public const SERVICE_HAPTONOMIE = 'service-haptonomie';
    public const SERVICE_YOGA_PRENATAL = 'service-yoga-prenatal';
    public const SERVICE_REEDUCATION_PERINEALE = 'service-reeducation-perineale';
    public const SERVICE_CONSULTATION_POSTNATALE = 'service-consultation-postnatale';
    public const SERVICE_ALLAITEMENT = 'service-allaitement';
    public const SERVICE_EXAMEN_NOUVEAU_NE = 'service-examen-nouveau-ne';
    public const SERVICE_CONSULTATION_NOURRISSON = 'service-consultation-nourrisson';

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $data = [
            [
                'name'            => 'Grossesse & Obstétrique',
                'metaTitle'       => 'Suivi de grossesse à Chevigny-Saint-Sauveur',
                'metaDescription' => 'Accompagnement complet de votre grossesse à Dijon : consultations prénatales, monitoring fœtal et suivi médical personnalisé par votre sage-femme.',
                'ref'             => self::DOMAIN_GROSSESSE,
                'services'        => [
                    [
                        'name'        => 'Suivi de grossesse',
                        'description' => 'Votre sage-femme vous accompagne tout au long de votre grossesse, de la déclaration de grossesse jusqu\'à l\'accouchement. Chaque consultation comprend la mesure du fond utérin, l\'auscultation des bruits du cœur fœtal, la vérification des résultats d\'analyses et la réponse à toutes vos questions.',
                        'position'    => 1,
                        'ref'         => self::SERVICE_SUIVI_GROSSESSE,
                    ],
                    [
                        'name'        => 'Consultation prénatale',
                        'description' => 'Les consultations prénatales permettent de surveiller l\'évolution de votre grossesse et la bonne santé de votre bébé. Nous réalisons l\'ensemble des examens réglementaires et assurons la coordination avec votre équipe médicale (gynécologue, obstétricien, anesthésiste).',
                        'position'    => 2,
                        'ref'         => self::SERVICE_CONSULTATION_PRENATALE,
                    ],
                    [
                        'name'        => 'Monitoring fœtal',
                        'description' => 'Le monitoring (ou cardiotocographie) permet d\'enregistrer simultanément le rythme cardiaque de votre bébé et les contractions utérines. Cet examen, réalisé à domicile ou au cabinet à partir de 36 semaines d\'aménorrhée, garantit le bien-être fœtal en fin de grossesse.',
                        'position'    => 3,
                        'ref'         => self::SERVICE_MONITORING,
                    ],
                ],
            ],
            [
                'name'            => 'Gynécologie',
                'metaTitle'       => 'Suivi gynécologique — Cabinet Albandea-Gauthier Dijon',
                'metaDescription' => 'Consultations gynécologiques, contraception, dépistage et suivi de la santé de la femme à Chevigny-Saint-Sauveur par une sage-femme libérale.',
                'ref'             => self::DOMAIN_GYNECOLOGIE,
                'services'        => [
                    [
                        'name'        => 'Suivi gynécologique',
                        'description' => 'La sage-femme est compétente pour assurer le suivi gynécologique de prévention des femmes en bonne santé tout au long de leur vie. Cela comprend les consultations de routine, le dépistage des infections et la surveillance de l\'équilibre hormonal.',
                        'position'    => 1,
                        'ref'         => self::SERVICE_SUIVI_GYNECO,
                    ],
                    [
                        'name'        => 'Contraception',
                        'description' => 'Prescription, pose et retrait des moyens de contraception : pilule, patch, anneau vaginal, implant contraceptif, stérilet au cuivre ou hormonal (DIU). La sage-femme vous conseille la méthode la plus adaptée à votre mode de vie et à votre santé.',
                        'position'    => 2,
                        'ref'         => self::SERVICE_CONTRACEPTION,
                    ],
                    [
                        'name'        => 'Frottis cervico-utérin',
                        'description' => 'Le frottis cervico-utérin est un examen de dépistage du cancer du col de l\'utérus recommandé tous les 3 ans (entre 25 et 65 ans). Réalisé au cabinet en quelques minutes, il est indolore et peut être couplé à votre consultation de suivi gynécologique.',
                        'position'    => 3,
                        'ref'         => self::SERVICE_FROTTIS,
                    ],
                ],
            ],
            [
                'name'            => 'Préparation à la naissance',
                'metaTitle'       => 'Préparation à la naissance — Cabinet Albandea-Gauthier',
                'metaDescription' => 'Cours de préparation à la naissance, haptonomie et yoga prénatal à Chevigny-Saint-Sauveur. Préparez votre accouchement avec votre sage-femme.',
                'ref'             => self::DOMAIN_PREPARATION,
                'services'        => [
                    [
                        'name'        => 'Préparation classique à la naissance',
                        'description' => 'Les séances de préparation à la naissance et à la parentalité (PNP) couvrent les mécanismes de l\'accouchement, la gestion de la douleur, les techniques respiratoires et de poussée, les soins du nouveau-né et les premiers jours de vie à la maison. Remboursées par l\'Assurance Maladie (8 séances).',
                        'position'    => 1,
                        'ref'         => self::SERVICE_PREPARATION_CLASSIQUE,
                    ],
                    [
                        'name'        => 'Haptonomie prénatale',
                        'description' => 'L\'haptonomie est une méthode de communication affective avec le bébé avant la naissance. Par le toucher doux sur le ventre, vous et votre partenaire créez un lien avec votre enfant, favorisant son développement psycho-affectif et préparant en douceur l\'accouchement.',
                        'position'    => 2,
                        'ref'         => self::SERVICE_HAPTONOMIE,
                    ],
                    [
                        'name'        => 'Yoga prénatal',
                        'description' => 'Le yoga prénatal associe postures douces adaptées à la grossesse, techniques de respiration et relaxation. Ces séances en petit groupe permettent de soulager les inconforts courants (lombalgies, jambes lourdes), de renforcer le périnée et de préparer corps et esprit à l\'accouchement.',
                        'position'    => 3,
                        'ref'         => self::SERVICE_YOGA_PRENATAL,
                    ],
                ],
            ],
            [
                'name'            => 'Périnatalité & Post-partum',
                'metaTitle'       => 'Rééducation périnéale & post-partum — Dijon',
                'metaDescription' => 'Rééducation périnéale, consultation post-natale et soutien à l\'allaitement à Chevigny-Saint-Sauveur. Reprenez confiance après votre accouchement.',
                'ref'             => self::DOMAIN_PERINATALITE,
                'services'        => [
                    [
                        'name'        => 'Rééducation périnéale',
                        'description' => 'La rééducation du périnée est recommandée après tout accouchement (voie basse ou césarienne) pour prévenir les fuites urinaires, les prolapsus et les douleurs pelviennes. Nos séances combinent biofeedback, électrostimulation et exercices actifs personnalisés. Remboursée par la Sécurité Sociale (10 séances).',
                        'position'    => 1,
                        'ref'         => self::SERVICE_REEDUCATION_PERINEALE,
                    ],
                    [
                        'name'        => 'Consultation post-natale',
                        'description' => 'La visite post-natale (entre 6 et 8 semaines après l\'accouchement) fait le bilan de votre état de santé physique et psychologique. Nous abordons la contraception, la reprise des activités, le dépistage du baby-blues et de la dépression post-partum, ainsi que toute question liée à votre rétablissement.',
                        'position'    => 2,
                        'ref'         => self::SERVICE_CONSULTATION_POSTNATALE,
                    ],
                    [
                        'name'        => 'Soutien à l\'allaitement',
                        'description' => 'La mise en place de l\'allaitement maternel peut nécessiter un accompagnement personnalisé. La sage-femme vous aide à trouver les bonnes positions, à évaluer la prise du sein, à prévenir et traiter les complications (crevasses, engorgement, mastite) et à concilier allaitement et vie quotidienne.',
                        'position'    => 3,
                        'ref'         => self::SERVICE_ALLAITEMENT,
                    ],
                ],
            ],
            [
                'name'            => 'Nouveau-né & Nourrisson',
                'metaTitle'       => 'Suivi du nouveau-né — Cabinet Albandea-Gauthier Dijon',
                'metaDescription' => 'Examen du nouveau-né et consultation du nourrisson à domicile ou au cabinet de Chevigny-Saint-Sauveur. Votre sage-femme assure le suivi de votre bébé.',
                'ref'             => self::DOMAIN_NOUVEAU_NE,
                'services'        => [
                    [
                        'name'        => 'Examen du nouveau-né',
                        'description' => 'L\'examen clinique du nouveau-né est réalisé idéalement dans les premières heures de vie et lors des visites à domicile. Il comprend la vérification des réflexes archaïques, du tonus, de la croissance, du bilan ombilical et de l\'alimentation, qu\'elle soit au sein ou au biberon.',
                        'position'    => 1,
                        'ref'         => self::SERVICE_EXAMEN_NOUVEAU_NE,
                    ],
                    [
                        'name'        => 'Consultation du nourrisson',
                        'description' => 'Jusqu\'aux 28 jours de vie, la sage-femme peut assurer le suivi médical du nourrisson. Ces consultations permettent de surveiller la courbe de poids, l\'état de la fontanelle, le bon développement neuromoteur et de répondre aux inquiétudes des jeunes parents sur les soins quotidiens.',
                        'position'    => 2,
                        'ref'         => self::SERVICE_CONSULTATION_NOURRISSON,
                    ],
                ],
            ],
        ];

        foreach ($data as $domainData) {
            $domain = new Domain();
            $domain
                ->setName($domainData['name'])
                ->setMetaTitle($domainData['metaTitle'])
                ->setMetaDescription($domainData['metaDescription'])
            ;
            $manager->persist($domain);
            $this->addReference($domainData['ref'], $domain);

            foreach ($domainData['services'] as $pos => $svcData) {
                $service = new Service();
                $service
                    ->setName($svcData['name'])
                    ->setDescription($svcData['description'])
                    ->setPosition($svcData['position'])
                    ->setDomain($domain)
                ;
                $manager->persist($service);
                $this->addReference($svcData['ref'], $service);
            }
        }

        $manager->flush();
    }

    #[\Override]
    public static function getGroups(): array
    {
        return ['domainGroup'];
    }
}
