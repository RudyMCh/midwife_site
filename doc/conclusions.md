# Conclusions et perspectives — projet SageFemme

_Analyse réalisée le 2026-04-08 — après complétion des 8 étapes des directives_

---

## Ce qui a été accompli

Les 8 étapes des directives sont toutes complétées et commitées :

| Étape | Commit | Résultat |
|---|---|---|
| Analyse + doc | — | `doc/analyse.md`, `doc/filemanager.md`, `doc/frontend.md`, `doc/docker.md` |
| Docker | `feat(docker)` | PHP 8.4-fpm-alpine, MariaDB 11.4, Nginx, Mailcatcher |
| PHP-CS-Fixer | `feat: ajout php-cs-fixer` | Ruleset `@Symfony`, CI-ready |
| Rector / PHPStan | `refactor(rector-p1..p4)` + `fix: PHPStan level 8` | 0 erreur PHPStan level 8 |
| Symfony 7.4 / PHP 8.4 | `feat(upgrade)` | Doctrine ORM 3, attributs PHP 8 |
| Suppression FileManagerBundle | `feat(step6)` | `ImageUploadService` natif, `MediaFile`, `MediaExtension` |
| SEO | `feat(seo)` | Sitemap XML, meta tags, structured data |
| Refactorisation | `refactor(step8)` | Code mort retiré, `Tools` allégé |

La stack cible est atteinte :
- **PHP 8.4** / **Symfony 7.4** / **Doctrine ORM 3**
- **AssetMapper** + `symfonycasts/sass-bundle` (Webpack Encore supprimé)
- **TinyMCE v6** (CKEditor 4 supprimé), **Tom Select** (Select2 supprimé), **Vanilla JS** (jQuery supprimé)
- **PHPStan level 8** : 0 erreur
- **Tests unitaires** : `ImageUploadServiceTest`, `MediaExtensionTest`

---

## Points forts du projet actuel

### Architecture
- **Pattern Handler/Controller** cohérent sur toute l'administration : les controllers sont minces, la logique persist/flush est dans `src/Form/Handler/`.
- **MediaFile** bien conçu : entité simple avec `getPath()`, gère images, vidéos, iframes. `MediaExtension` Twig propre (3 fonctions : `media_path`, `media_render`, `media_thumb`).
- **ImageUploadService** : gestion complète upload → compression → resize → thumbnail, avec constantes bien nommées et séparation des responsabilités.
- **Sitemap XML** dynamique avec priorités différenciées par type de page.
- **Sécurité** solide : throttling login, CSRF, hiérarchie de rôles, reset password via SymfonyCasts.
- **Docker** soigné : volumes nommés pour uploads/thumbs/securedSpace, séparation base/override dev.

### Qualité de code
- PHPStan level 8 respecté — tous les types sont explicites.
- PHP-CS-Fixer configuré — style cohérent.
- PHP 8.4 features utilisées : `readonly` properties, `match`, attributs natifs `#[Override]`, `#[Autowire]`.

---

## Dettes techniques restantes

### Urgentes

**1. `HomePageController` non migré vers les attributs PHP 8**
[HomePageController.php](src/Controller/FrontController/HomePageController.php) conserve les imports `use Symfony\Component\Routing\Annotation\Route` (annotation deprecated) en parallèle des attributs. Les PHPDoc `@param`/`@return` sont superflus avec les types PHP 8 natifs. La récupération `$office = $office[0]` et `$homePage = $homePage[0]` sans null check est fragile — si la BDD est vide, exception fatale.

**2. Gedmo Slug en annotation docblock dans `MediaFile` et `Midwife`**
[MediaFile.php:37](src/Entity/MediaFile.php#L37) et [Midwife.php:25](src/Entity/Midwife.php#L25) utilisent encore `@Gedmo\Slug(...)` en docblock. C'est une limitation connue de `stof/doctrine-extensions-bundle` ^1.12 : le support PHP 8 attributes pour Gedmo Slug est disponible depuis la version `^1.10` mais nécessite un ajout explicite de l'annotation driver. À corriger pour cohérence et pour éviter une rupture lors d'une future suppression du support docblock.

**3. `@Assert\CssColor` en docblock dans `Midwife`**
[Midwife.php:42](src/Entity/Midwife.php#L42) — idem, à migrer en `#[Assert\CssColor]`.

**4. `CompressingImagesCommand` — logique incohérente**
[CompressingImagesCommand.php](src/Command/CompressingImagesCommand.php) compresse toutes les images à 200×200px maximum avec qualité JPEG 100 — c'est un resize thumbnail, pas une compression. Devrait déléguer à `ImageUploadService` avec ses constantes (`MAX_WIDTH=2000`, `MAX_WEIGHT=200ko`). En l'état, la commande dégrade les images de production.

**5. `ImageUploadService` : thumbnails toujours en JPEG**
[ImageUploadService.php:109](src/Service/ImageUploadService.php#L109) — `imagejpeg()` pour les thumbnails PNG/GIF perd la transparence malgré l'alpha handling. Utiliser `imagepng()` pour les PNG.

### Moyennes

**6. Mentions légales professionnelles incomplètes**
L'entité `InformationPage` a un champ `mention` de 255 caractères — insuffisant. Les champs obligatoires pour un professionnel de santé libéral sont absents de l'entité `Midwife` :
- Numéro RPPS (11 chiffres)
- Numéro ADELI (9 chiffres)
- Assurance RCP (libellé + numéro contrat)
- Numéro ordinal (Ordre des Sages-Femmes)
- SIRET

**7. Sitemap sans `<lastmod>`**
[SitemapController.php](src/Controller/FrontController/SitemapController.php) génère des URLs sans date de dernière modification. Les entités ont `updatedAt` via `TimestampableEntity` — l'utiliser.

**8. `robots.txt` absent**
Aucune route `GET /robots.txt`. Le fichier doit au minimum contenir `Sitemap: https://domaine.fr/sitemap.xml` et les règles `Disallow` pour `/admin/`.

**9. `PropertyInfoExtractor` avec la classe `Type` deprecated**
[PropertyInspectorService.php:9](src/Services/PropertyInspectorService.php#L9) importe `Symfony\Component\PropertyInfo\Type` qui est deprecated en Symfony 7 (remplacé par `PHPStan\Type` / `Symfony\Component\TypeInfo`). À surveiller pour Symfony 8.

### Faibles (code mort / cosmétique)

**10. `Tools` service — utilité discutable**
[Tools.php](src/Services/Tools.php) ne fait que proxifier `PropertyInspectorService::getProperties()`. Si `Tools` n'est utilisé qu'à un seul endroit, injecter `PropertyInspectorService` directement et supprimer `Tools`.

**11. `MediaFile::$slug` — champ non utilisé côté app**
Le slug de `MediaFile` est généré par Gedmo mais aucun controller ni template ne l'utilise. Si inutile, retirer Gedmo Slug sur cette entité.

---

## Axes d'amélioration possibles

### SEO (haute valeur)

**Données structurées JSON-LD manquantes ou partielles**
Le step 7 a posé les bases meta tags, mais les schémas `schema.org` les plus impactants pour ce métier n'ont pas été vérifiés :
- `MedicalOrganization` pour le cabinet (Office)
- `Physician` / `MedicalBusiness` pour chaque sage-femme
- `MedicalSpecialty` pour les domaines
- `FAQPage` si la page information est structurée Q/R

Ces schémas permettent des **rich snippets** (extraits enrichis Google) directement liés à l'objectif de remplissage du planning.

**Open Graph et Twitter Cards**
Vérifier que chaque page a les balises `og:image`, `og:type`, `og:description` renseignées. Les fiches sage-femme partagées sur les réseaux sociaux bénéficieraient d'une preview avec photo.

**`<link rel="canonical">`**
Essentiel si le site est accessible en HTTP et HTTPS, ou avec/sans `www`. À ajouter dans le `base.html.twig` de façon dynamique.

**Core Web Vitals**
- Toutes les images sans `width`/`height` explicites causent du CLS (Cumulative Layout Shift).
- `loading="lazy"` à vérifier sur toutes les images non above-the-fold.
- La conversion en **WebP** lors de l'upload (dans `ImageUploadService`) réduirait le poids moyen de 30-50% sans perte visible.

### Fonctionnel

**Formulaire de contact**
Aucune page de contact n'existe. Un formulaire simple (nom, email, message) avec envoi vers l'email du cabinet serait une conversion directe. Symfony Mailer est déjà installé, `symfony/form` aussi.

**Page 404 personnalisée**
`bundles/TwigBundle/Exception/error404.html.twig` existe mais son contenu n'a pas été vérifié — s'assurer qu'elle propose un lien vers Doctolib et la liste des domaines.

**Gestion des fichiers côté admin**
Le remplacement du FileManagerBundle par `ImageUploadService` fonctionne pour l'upload initial, mais l'admin ne peut plus :
- Parcourir les fichiers déjà uploadés pour les réassigner
- Supprimer des fichiers orphelins (sans entité liée)
- Renommer ou ajouter un `alt` à une image existante

Un contrôleur admin simple `MediaFileController` avec liste + édition des métadonnées (`alt`, `title`) serait utile.

**Soft delete pour les entités**
Actuellement, supprimer une sage-femme supprime définitivement son profil. Un `SoftDeleteable` Gedmo permettrait de conserver l'historique et d'éviter des 404 sur des URLs indexées.

### Technique

**WebP à l'upload**
Modifier `ImageUploadService::makeThumb()` et les méthodes `resizeUploadedImage`/`compressUploadedImage` pour produire des fichiers `.webp` (PHP GD supporte `imagewebp()` depuis PHP 5.4). Conserver l'original en fallback si nécessaire.

**Tests d'intégration sur les controllers**
Actuellement seuls `ImageUploadService` et `MediaExtension` sont testés en unitaire. Des tests d'intégration sur les controllers front (au moins `HomePageController`, `MidwifeController`) permettraient de détecter des régressions sans environnement complet. `symfony/browser-kit` est déjà en require-dev.

**Cache HTTP sur les pages front**
Les pages front (homepage, fiche sage-femme, domaine) sont statiques entre deux modifications admin. Ajouter des headers `Cache-Control: public, max-age=3600` + `ETag`/`Last-Modified` dans les controllers front réduirait la charge serveur et améliorerait le TTFB.

**Matomo en mode cookieless**
La directive mentionne Matomo. Sur o2switch, Matomo peut être configuré en mode anonymisé (sans cookie) pour se passer du consentement RGPD. À documenter et implémenter dans le tag Matomo côté template.

**Gestion des images AVIF**
PHP GD 8.1+ supporte AVIF (`imagecreatefromavif`, `imageavif`). En complément du WebP, AVIF offre 20-30% de gain supplémentaire sur les navigateurs modernes. L'`ImageUploadService` pourrait détecter le support et générer le format optimal.

---

## Priorisation suggérée

| Priorité | Action | Impact | Effort |
|---|---|---|---|
| 1 | Mentions légales professionnelles (RPPS, ADELI...) dans `Midwife` | Légal / conformité | Moyen |
| 2 | `robots.txt` + `<lastmod>` dans sitemap | SEO immédiat | Faible |
| 3 | `CompressingImagesCommand` — corriger la logique | Qualité / prod | Faible |
| 4 | Formulaire de contact | Conversion | Moyen |
| 5 | JSON-LD `MedicalOrganization` + `Physician` | SEO rich snippets | Moyen |
| 6 | `MediaFileController` admin (liste + édition alt/title) | UX admin | Moyen |
| 7 | WebP à l'upload | Performance / SEO | Moyen |
| 8 | Tests d'intégration controllers front | Qualité | Moyen |
| 9 | `HomePageController` — null check + nettoyage | Robustesse | Faible |
| 10 | Gedmo Slug → attributs PHP 8 | Cohérence | Faible |

---

## Note sur le déploiement (o2switch)

o2switch est un hébergement mutualisé PHP — **pas de Docker en production**. L'environnement Docker est donc uniquement pour le développement local. Pour la production :
- Configurer les variables d'environnement via le panneau o2switch ou un `.env.local` non versionné.
- `composer dump-env prod` pour optimiser le chargement des variables.
- Vérifier que PHP 8.4 est disponible sur o2switch (certains plans sont encore sur 8.1/8.2 — demander l'activation via le support).
- AssetMapper génère les assets en statique (`bin/console asset-map:compile`) — à intégrer dans le process de déploiement.
- La commande `importmap:install` du `composer.json` doit être exécutée après chaque `composer install` en production.
