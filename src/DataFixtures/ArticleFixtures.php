<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Midwife;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        /** @var Midwife $chloe */
        $chloe = $this->getReference(MidwifeFixtures::MIDWIFE_CHLOE, Midwife::class);
        /** @var Midwife $aurelie */
        $aurelie = $this->getReference(MidwifeFixtures::MIDWIFE_AURELIE, Midwife::class);

        $articles = $this->getArticlesData($chloe, $aurelie);

        foreach ($articles as $data) {
            $article = (new Article())
                ->setTitle($data['title'])
                ->setContent($data['content'])
                ->setExcerpt($data['excerpt'] ?? null)
                ->setIsPublished($data['isPublished'])
                ->setPublishedAt($data['publishedAt'] ?? null)
                ->setMetaTitle($data['metaTitle'] ?? null)
                ->setMetaDescription($data['metaDescription'] ?? null)
                ->setAuthor($data['author'])
            ;
            $manager->persist($article);
        }

        $manager->flush();
    }

    /**
     * @param Midwife $chloe
     * @param Midwife $aurelie
     * @return array<int, array<string, mixed>>
     */
    private function getArticlesData(Midwife $chloe, Midwife $aurelie): array
    {
        $now = new \DateTimeImmutable('2026-04-12');

        return [
            // ── Article 1 — publié il y a ~6 mois ──────────────────────────────
            [
                'title' => 'Les bienfaits de la préparation à la naissance',
                'excerpt' => 'Pourquoi se préparer à l\'accouchement ? Découvrez les différentes méthodes proposées par votre sage-femme pour aborder sereinement ce grand jour.',
                'content' => <<<HTML
<h2>Pourquoi se préparer à la naissance&nbsp;?</h2>
<p>La préparation à la naissance est bien plus qu'un simple cours d'accouchement. Elle vous permet de comprendre les mécanismes physiologiques du travail, de développer des techniques de gestion de la douleur et de renforcer votre confiance en vous avant le grand jour.</p>
<p>Elle s'adresse aux futures mamans dès le 7<sup>e</sup> mois de grossesse, mais aussi à leur partenaire, qui joue un rôle fondamental durant le travail et le post-partum.</p>

<h2>Les méthodes proposées au cabinet</h2>
<h3>La méthode classique</h3>
<p>Lors de ces séances en groupe ou en individuel, votre sage-femme vous explique le déroulement du travail, les différentes phases de l'accouchement et les techniques de respiration. Vous apprendrez à reconnaître les contractions, à gérer leur intensité et à communiquer efficacement avec l'équipe médicale.</p>

<h3>L'haptonomie</h3>
<p>L'haptonomie est une approche affective et sensorielle qui permet d'établir un lien profond avec votre bébé avant la naissance. À travers le toucher et la voix, vous invitez votre enfant à se déplacer dans le ventre maternel. Le futur père y trouve une place centrale dans cet accompagnement.</p>

<h3>Le yoga prénatal</h3>
<p>Combinant postures douces, respiration et relaxation, le yoga prénatal soulage les inconforts de la grossesse (douleurs lombaires, jambes lourdes) et prépare le corps à l'accouchement. Les séances favorisent également la détente mentale et le sommeil.</p>

<h2>Combien de séances sont remboursées&nbsp;?</h2>
<p>L'Assurance Maladie prend en charge <strong>7 séances de préparation à la naissance</strong> à 100 % dès le 6<sup>e</sup> mois de grossesse. Ces séances peuvent être réalisées par une sage-femme de votre choix.</p>
<ul>
    <li>Séances individuelles ou en couple</li>
    <li>Possibilité de panacher les méthodes selon vos envies</li>
    <li>Prise en charge dès le 6<sup>e</sup> mois</li>
</ul>

<h2>Comment prendre rendez-vous&nbsp;?</h2>
<p>Vous pouvez réserver votre première séance directement via Doctolib ou en nous appelant au cabinet. Nous vous conseillons de commencer votre préparation dès le 7<sup>e</sup> mois pour avoir le temps de suivre toutes les séances avant le terme.</p>
HTML,
                'isPublished' => true,
                'publishedAt' => \DateTime::createFromImmutable($now->modify('-6 months')),
                'metaTitle' => 'Préparation à la naissance à Chevigny-Saint-Sauveur — Cabinet Albandea-Gauthier',
                'metaDescription' => 'Découvrez les méthodes de préparation à la naissance proposées par nos sages-femmes : méthode classique, haptonomie et yoga prénatal. 7 séances remboursées.',
                'author' => $chloe,
            ],

            // ── Article 2 — publié il y a ~5 mois ──────────────────────────────
            [
                'title' => 'La rééducation périnéale : pourquoi et comment ?',
                'excerpt' => 'Après l\'accouchement, la rééducation périnéale est indispensable pour retrouver un périnée tonique. Votre sage-femme vous explique tout.',
                'content' => <<<HTML
<h2>Le périnée, cet ensemble musculaire méconnu</h2>
<p>Le périnée est un ensemble de muscles et de ligaments situé entre le pubis et le coccyx. Il soutient les organes pelviens (vessie, utérus, rectum) et joue un rôle clé dans la continence urinaire et fécale, ainsi que dans la vie sexuelle.</p>
<p>Lors de la grossesse, le périnée supporte un poids croissant pendant neuf mois. L'accouchement par voie basse, et parfois même la grossesse elle-même en cas de naissance par césarienne, peut l'affaiblir ou le traumatiser.</p>

<h2>Quand commencer la rééducation&nbsp;?</h2>
<p>La rééducation périnéale est prescrite lors de la visite post-natale, généralement entre 6 et 8 semaines après l'accouchement. Il est important d'attendre la cicatrisation complète avant de débuter les séances.</p>
<p>En France, <strong>10 séances de rééducation périnéale sont remboursées</strong> à 100 % par l'Assurance Maladie après chaque accouchement.</p>

<h2>Le déroulement d'une séance</h2>
<p>Chaque séance dure environ 30 minutes. Votre sage-femme utilise différentes techniques :</p>
<ul>
    <li><strong>Exercices manuels</strong> : prise de conscience et renforcement musculaire par des contractions guidées</li>
    <li><strong>Électrostimulation</strong> : courant électrique doux qui stimule les fibres musculaires de manière passive</li>
    <li><strong>Biofeedback</strong> : sonde qui mesure les contractions en temps réel pour visualiser les progrès</li>
</ul>

<h2>Les bénéfices à long terme</h2>
<p>Une rééducation bien menée permet de :</p>
<ul>
    <li>Corriger les fuites urinaires à l'effort (éternuement, toux, sport)</li>
    <li>Prévenir les prolapsus génitaux</li>
    <li>Améliorer la qualité de vie sexuelle</li>
    <li>Reprendre le sport en toute sécurité</li>
</ul>
<p>Ne négligez pas cette étape essentielle du post-partum. N'hésitez pas à en parler à votre sage-femme dès votre visite de grossesse.</p>
HTML,
                'isPublished' => true,
                'publishedAt' => \DateTime::createFromImmutable($now->modify('-5 months')),
                'metaTitle' => 'Rééducation périnéale après l\'accouchement — Sage-femme Dijon',
                'metaDescription' => 'Tout savoir sur la rééducation périnéale après l\'accouchement : quand commencer, comment se déroulent les séances, et pourquoi c\'est indispensable.',
                'author' => $aurelie,
            ],

            // ── Article 3 — publié il y a ~4 mois ──────────────────────────────
            [
                'title' => 'Suivi gynécologique par une sage-femme : ce que vous pouvez faire',
                'excerpt' => 'La sage-femme est habilitée à assurer le suivi gynécologique de prévention des femmes en bonne santé. Frottis, contraception, ménopause : découvrez l\'étendue de nos compétences.',
                'content' => <<<HTML
<h2>Un suivi gynécologique complet, pas seulement pour la grossesse</h2>
<p>La sage-femme est un professionnel de santé médical dont les compétences vont bien au-delà de l'accompagnement de la grossesse. Depuis 2009, les sages-femmes sont habilitées à assurer le <strong>suivi gynécologique de prévention</strong> des femmes en bonne santé tout au long de leur vie.</p>

<h2>Ce que réalise votre sage-femme</h2>
<h3>Les frottis cervico-utérins</h3>
<p>Le frottis permet de dépister les lésions précancéreuses du col de l'utérus. Il est recommandé à partir de 25 ans, tous les 3 ans après deux frottis normaux réalisés à un an d'intervalle. Votre sage-femme réalise ce geste au cabinet, dans un cadre rassurant.</p>

<h3>La contraception</h3>
<p>La sage-femme peut prescrire, adapter et renouveler votre contraception :</p>
<ul>
    <li>Contraceptifs oraux (pilule)</li>
    <li>Implant contraceptif (pose et retrait)</li>
    <li>Dispositif intra-utérin — DIU (pose et retrait)</li>
    <li>Contraception d'urgence</li>
    <li>Patch et anneau vaginal</li>
</ul>

<h3>Le suivi de ménopause</h3>
<p>La sage-femme accompagne les femmes dans la période de périménopause et ménopause : consultation de suivi, évaluation des symptômes et orientation vers un médecin si nécessaire.</p>

<h2>Quand consulter votre gynécologue plutôt que votre sage-femme&nbsp;?</h2>
<p>La sage-femme prend en charge les femmes en bonne santé sans pathologie gynécologique avérée. En cas de symptômes anormaux (saignements inexpliqués, douleurs pelviennes chroniques, antécédents oncologiques), la consultation d'un gynécologue médical ou d'un gynécologue-obstétricien s'impose.</p>
<p>Nous travaillons en réseau avec les spécialistes de Dijon pour vous orienter rapidement si besoin.</p>
HTML,
                'isPublished' => true,
                'publishedAt' => \DateTime::createFromImmutable($now->modify('-4 months')),
                'metaTitle' => 'Suivi gynécologique par une sage-femme à Chevigny-Saint-Sauveur',
                'metaDescription' => 'Frottis, contraception, implant, DIU, suivi de ménopause : votre sage-femme assure votre suivi gynécologique de prévention. Prenez rendez-vous.',
                'author' => $chloe,
            ],

            // ── Article 4 — publié il y a ~3 mois ──────────────────────────────
            [
                'title' => 'L\'entretien prénatal précoce : un rendez-vous clé au 1er trimestre',
                'excerpt' => 'L\'entretien prénatal précoce (EPP) est proposé à toutes les femmes enceintes dès le 1er trimestre. Un espace de parole confidentiel pour préparer votre grossesse en toute sérénité.',
                'content' => <<<HTML
<h2>Qu'est-ce que l'entretien prénatal précoce&nbsp;?</h2>
<p>L'entretien prénatal précoce (EPP) est un rendez-vous individuel ou en couple, proposé à toutes les femmes enceintes <strong>avant la fin du 4<sup>e</sup> mois de grossesse</strong>. Il est réalisé par une sage-femme ou un médecin et dure environ 45 minutes à 1 heure.</p>
<p>Cet entretien n'est pas un examen médical. C'est un espace de parole, confidentiel et bienveillant, pour aborder vos questions, vos attentes et vos éventuelles inquiétudes concernant la grossesse, l'accouchement et l'arrivée de votre bébé.</p>

<h2>Pourquoi c'est important&nbsp;?</h2>
<p>De nombreuses femmes arrivent à l'accouchement avec des représentations anxiogènes (douleur, perte de contrôle, complications) qui ne correspondent pas à la réalité de la grande majorité des naissances. L'EPP permet de dédramatiser, d'informer et de construire un projet de naissance réaliste.</p>
<p>Il permet également de repérer précocement les situations de vulnérabilité (isolement social, antécédents de violence, difficultés psychologiques) pour proposer un accompagnement adapté.</p>

<h2>Ce dont on parle lors de l'EPP</h2>
<ul>
    <li>Votre projet de naissance (lieu d'accouchement, analgésie, présence du partenaire)</li>
    <li>Votre rapport à la douleur et vos craintes</li>
    <li>L'allaitement : choix, informations pratiques</li>
    <li>L'organisation du retour à domicile et le soutien disponible</li>
    <li>Les questions administratives (congé maternité, PAJE, mode de garde)</li>
    <li>Votre bien-être psychologique</li>
</ul>

<h2>Comment prendre rendez-vous&nbsp;?</h2>
<p>L'EPP est <strong>entièrement pris en charge par l'Assurance Maladie</strong>. Vous pouvez prendre rendez-vous avec Aurélie Albandea directement via Doctolib, en précisant « Entretien prénatal précoce ».</p>
<p>Nous vous invitons à venir accompagnée de votre partenaire si vous le souhaitez. Cet entretien est aussi l'occasion pour lui de poser toutes ses questions.</p>
HTML,
                'isPublished' => true,
                'publishedAt' => \DateTime::createFromImmutable($now->modify('-3 months')),
                'metaTitle' => 'Entretien prénatal précoce à Chevigny-Saint-Sauveur — Sage-femme Aurélie Albandea',
                'metaDescription' => 'Qu\'est-ce que l\'entretien prénatal précoce (EPP) ? Un espace de parole dès le 1er trimestre pour préparer votre grossesse sereinement. 100 % remboursé.',
                'author' => $aurelie,
            ],

            // ── Article 5 — publié il y a ~2 mois ──────────────────────────────
            [
                'title' => 'Allaitement : les clés d\'un démarrage réussi',
                'excerpt' => 'L\'allaitement maternel est une expérience unique mais qui peut s\'avérer difficile les premiers jours. Nos sages-femmes vous accompagnent pour un démarrage serein.',
                'content' => <<<HTML
<h2>Les premières heures après la naissance</h2>
<p>La mise au sein dans les premières heures suivant la naissance est fondamentale. Le colostrum, ce lait épais et doré des premiers jours, est particulièrement riche en anticorps et en nutriments. Il est parfaitement adapté aux besoins du nouveau-né et suffit à le nourrir.</p>
<p>Ne vous inquiétez pas si votre montée de lait prend 2 à 4 jours. C'est tout à fait normal.</p>

<h2>La prise du sein : le point le plus important</h2>
<p>La quasi-totalité des difficultés d'allaitement (crevasses, engorgement, sein douloureux) viennent d'une mauvaise prise du sein. Pour un allaitement confortable :</p>
<ul>
    <li>La bouche de bébé doit être grande ouverte, comme pour un bâillement</li>
    <li>Il doit prendre le mamelon <em>et</em> une bonne partie de l'aréole</li>
    <li>Ses lèvres sont retroussées vers l'extérieur</li>
    <li>Son menton touche le sein, son nez est dégagé</li>
    <li>La tétée ne doit pas être douloureuse si la position est correcte</li>
</ul>

<h2>Combien de tétées par jour&nbsp;?</h2>
<p>Un nouveau-né tète en moyenne <strong>8 à 12 fois par 24 heures</strong> les premières semaines. Les tétées fréquentes stimulent la production de lait. Ne limitez pas leur durée ni leur fréquence.</p>
<p>Le rythme se régularise naturellement entre 4 et 8 semaines.</p>

<h2>Quand consulter votre sage-femme&nbsp;?</h2>
<p>N'attendez pas pour consulter si vous ressentez :</p>
<ul>
    <li>Des crevasses ou douleurs persistantes</li>
    <li>Un engorgement ou une mastite (sein rouge, chaud, fièvre)</li>
    <li>L'impression que bébé ne prend pas assez de poids</li>
    <li>Un doute sur votre production de lait</li>
</ul>
<p>Nos sages-femmes sont référentes allaitement. N'hésitez pas à nous appeler ou à prendre rendez-vous en urgence via Doctolib.</p>
HTML,
                'isPublished' => true,
                'publishedAt' => \DateTime::createFromImmutable($now->modify('-2 months')),
                'metaTitle' => 'Allaitement maternel : conseils de vos sages-femmes à Dijon',
                'metaDescription' => 'Prise du sein, fréquence des tétées, crevasses, engorgement : nos sages-femmes vous guident pour un allaitement serein dès les premiers jours.',
                'author' => $aurelie,
            ],

            // ── Article 6 — publié il y a ~6 semaines ──────────────────────────
            [
                'title' => 'Acupuncture obstétricale : déclenchement naturel et inconforts de fin de grossesse',
                'excerpt' => 'L\'acupuncture peut soulager de nombreux inconforts en fin de grossesse et favoriser le déclenchement naturel du travail. Chloé Gauthier, formée à l\'acupuncture obstétricale, vous explique.',
                'content' => <<<HTML
<h2>L'acupuncture obstétricale, qu'est-ce que c'est&nbsp;?</h2>
<p>L'acupuncture obstétricale est une branche spécialisée de l'acupuncture appliquée aux domaines de la grossesse et de la périnatalité. Elle repose sur la stimulation de points d'acupuncture par de fines aiguilles stériles afin de rétablir la circulation de l'énergie dans les méridiens.</p>
<p>Je suis titulaire du <strong>DU d'acupuncture obstétricale et néonatale</strong> de l'Université de Bourgogne (2018) et je pratique cette technique au cabinet depuis plusieurs années.</p>

<h2>Pour quelles indications&nbsp;?</h2>
<h3>Inconforts de fin de grossesse</h3>
<ul>
    <li><strong>Lombalgies et sciatiques</strong> : très fréquentes au 3<sup>e</sup> trimestre, elles répondent bien aux séances d'acupuncture</li>
    <li><strong>Nausées et vomissements</strong> : efficaces dès le 1<sup>er</sup> trimestre</li>
    <li><strong>Insomnie</strong> : pour retrouver un sommeil de qualité</li>
    <li><strong>Anxiété</strong> : détente profonde avant l'accouchement</li>
</ul>

<h3>Version par siège</h3>
<p>La technique de moxibustion (chaleur appliquée sur le point Zhiyin, dernier point du méridien de la vessie, à la pointe de l'auriculaire du pied) peut aider à retourner un bébé en siège avant 37 semaines. Elle est sans douleur et bien tolérée.</p>

<h3>Déclenchement naturel du travail</h3>
<p>À partir de 40 semaines d'aménorrhée, en accord avec l'équipe obstétricale, des points spécifiques peuvent être stimulés pour favoriser la maturation du col et déclencher le travail naturellement.</p>

<h2>Est-ce remboursé&nbsp;?</h2>
<p>Les séances d'acupuncture réalisées par une sage-femme ne sont actuellement pas remboursées par l'Assurance Maladie. Le tarif est de 45 € la séance. Pour en savoir plus ou prendre rendez-vous, contactez le cabinet.</p>
HTML,
                'isPublished' => true,
                'publishedAt' => \DateTime::createFromImmutable($now->modify('-6 weeks')),
                'metaTitle' => 'Acupuncture obstétricale à Chevigny-Saint-Sauveur — Chloé Gauthier',
                'metaDescription' => 'Acupuncture pour la grossesse : soulagement des lombalgies, nausées, bébé en siège, déclenchement naturel. Chloé Gauthier, sage-femme formée à l\'acupuncture obstétricale.',
                'author' => $chloe,
            ],

            // ── Article 7 — publié il y a ~1 mois ──────────────────────────────
            [
                'title' => 'Le monitoring fœtal : à quoi ça sert ?',
                'excerpt' => 'Le cardiotocographe (CTG) enregistre le rythme cardiaque fœtal et les contractions. Votre sage-femme vous explique pourquoi et quand cet examen est réalisé.',
                'content' => <<<HTML
<h2>Qu'est-ce que le monitoring fœtal&nbsp;?</h2>
<p>Le monitoring fœtal, ou cardiotocographie (CTG), est un examen qui enregistre simultanément le <strong>rythme cardiaque du bébé</strong> et les <strong>contractions utérines</strong>. Il est réalisé à l'aide de deux capteurs posés sur le ventre maternel maintenus par une ceinture souple.</p>
<p>L'examen dure généralement entre 20 et 30 minutes. Il est indolore et sans risque pour la mère et l'enfant.</p>

<h2>Pourquoi est-il prescrit&nbsp;?</h2>
<p>Le monitoring peut être prescrit dans différentes situations :</p>
<ul>
    <li>Grossesse à risque (hypertension, diabète gestationnel, retard de croissance)</li>
    <li>Contrôle de bien-être fœtal à partir de 41 semaines d'aménorrhée</li>
    <li>Diminution des mouvements actifs du fœtus perçus par la mère</li>
    <li>Contractions avant terme</li>
    <li>Surveillance d'une grossesse gémellaire</li>
</ul>

<h2>Comment lire le tracé&nbsp;?</h2>
<p>Un tracé normal présente :</p>
<ul>
    <li>Une fréquence de base entre 110 et 160 bpm</li>
    <li>Une variabilité (oscillations régulières) de 5 à 25 bpm</li>
    <li>Des accélérations du rythme en réponse aux mouvements du bébé</li>
    <li>Absence de décélérations prolongées</li>
</ul>
<p>Votre sage-femme interprète le tracé et vous explique les résultats en fin d'examen. En cas d'anomalie, une orientation vers la maternité peut être décidée rapidement.</p>

<h2>Monitoring au cabinet ou à domicile&nbsp;?</h2>
<p>Le monitoring peut être réalisé au cabinet ou lors d'une visite à domicile si votre état de santé le nécessite. N'hésitez pas à contacter le cabinet pour évaluer votre situation.</p>
HTML,
                'isPublished' => true,
                'publishedAt' => \DateTime::createFromImmutable($now->modify('-1 month')),
                'metaTitle' => 'Monitoring fœtal (CTG) : examen et surveillance par votre sage-femme',
                'metaDescription' => 'Le monitoring enregistre le rythme cardiaque de votre bébé et vos contractions. Votre sage-femme vous explique cet examen de surveillance de la grossesse.',
                'author' => $chloe,
            ],

            // ── Article 8 — publié il y a ~2 semaines ──────────────────────────
            [
                'title' => 'Retour à domicile après l\'accouchement : le rôle de votre sage-femme',
                'excerpt' => 'Les visites à domicile après l\'accouchement sont assurées par votre sage-femme dans le cadre du PRADO ou sur prescription. Un accompagnement sur mesure pour vous et votre bébé.',
                'content' => <<<HTML
<h2>Le PRADO : retour précoce à la maison</h2>
<p>Le Programme de Retour à Domicile (PRADO) de l'Assurance Maladie permet aux jeunes mamans de rentrer chez elles plus tôt après l'accouchement, sous réserve d'un état de santé satisfaisant pour la mère et l'enfant. Une sage-femme libérale prend le relais de la maternité dès le 2<sup>e</sup> jour.</p>
<p>Dans le cadre du PRADO, vous bénéficiez d'<strong>au moins deux visites à domicile</strong> dans les 10 premiers jours de vie de votre bébé.</p>

<h2>Ce que comprend la visite post-natale à domicile</h2>
<h3>Pour la maman</h3>
<ul>
    <li>Surveillance des suites de couches (cicatrice, involution utérine, saignements)</li>
    <li>Soutien à l'allaitement maternel ou aide à la lactation en cas d'allaitement artificiel</li>
    <li>Évaluation du bien-être psychologique (dépistage de la dépression du post-partum)</li>
    <li>Conseils nutritionnels et reprise d'activité</li>
</ul>
<h3>Pour le bébé</h3>
<ul>
    <li>Pesée et courbe de croissance</li>
    <li>Examen du nouveau-né (tonus, réflexes, cicatrisation du cordon)</li>
    <li>Surveillance de l'ictère néonatal (jaunisse)</li>
    <li>Conseils sur le sommeil, les soins quotidiens et les signes d'alerte</li>
</ul>

<h2>Comment s'organise le suivi&nbsp;?</h2>
<p>Idéalement, contactez-nous avant l'accouchement pour que nous puissions organiser votre suivi post-natal. En cas d'adhésion au PRADO, la maternité contacte directement la sage-femme à votre nom.</p>
<p>N'hésitez pas à nous appeler à tout moment si vous avez un doute ou une inquiétude. Nous sommes joignables par téléphone et sur Doctolib.</p>
HTML,
                'isPublished' => true,
                'publishedAt' => \DateTime::createFromImmutable($now->modify('-2 weeks')),
                'metaTitle' => 'Visites à domicile post-partum — Sage-femme Chevigny-Saint-Sauveur',
                'metaDescription' => 'Retour à domicile après accouchement (PRADO) : suivi maman et nouveau-né par votre sage-femme. Pesée, allaitement, cicatrice, dépression post-partum.',
                'author' => $aurelie,
            ],

            // ── Article 9 — brouillon ───────────────────────────────────────────
            [
                'title' => 'Yoga prénatal : séances collectives au cabinet',
                'excerpt' => null,
                'content' => <<<HTML
<h2>Prochaine session de yoga prénatal</h2>
<p>Nous proposons des séances de yoga prénatal en petit groupe (4 personnes maximum) chaque jeudi matin au cabinet. Les séances durent 1h et sont animées par Aurélie Albandea.</p>
<p>Aucune expérience en yoga n'est nécessaire. Les exercices sont adaptés à chaque trimestre de grossesse.</p>
<p><em>Article en cours de rédaction — dates de la prochaine session à confirmer.</em></p>
HTML,
                'isPublished' => false,
                'publishedAt' => null,
                'metaTitle' => null,
                'metaDescription' => null,
                'author' => $aurelie,
            ],

            // ── Article 10 — brouillon ──────────────────────────────────────────
            [
                'title' => 'Contraception après l\'accouchement : nos conseils',
                'excerpt' => null,
                'content' => <<<HTML
<h2>Reprendre une contraception après la naissance</h2>
<p>La question de la contraception se pose dès le post-partum immédiat. Selon que vous allaitez ou non, les options ne sont pas les mêmes. Cet article est en cours de finalisation.</p>
<p><em>Brouillon — à compléter avant publication.</em></p>
HTML,
                'isPublished' => false,
                'publishedAt' => null,
                'metaTitle' => null,
                'metaDescription' => null,
                'author' => $chloe,
            ],
        ];
    }

    #[\Override]
    public static function getGroups(): array
    {
        return ['blogGroup'];
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [MidwifeFixtures::class];
    }
}
