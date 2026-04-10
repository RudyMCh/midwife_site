# Conclusions et perspectives — projet SageFemme

> **Document archivé — 2026-04-10**
> Toutes les actions listées ici ont été traitées (étapes 1-9 complètes, environnement Docker fonctionnel, fixtures prêtes, site fonctionnel).
> La phase suivante est décrite dans `CLAUDE.md`.

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

> ✅ = corrigé dans l'étape 9 (voir [etape9-corrections.md](etape9-corrections.md))

### Urgentes

**1. `HomePageController` non migré vers les attributs PHP 8** ✅
Corrigé : import deprecated supprimé, docblocks retirés, null checks ajoutés.

**2. Gedmo Slug en annotation docblock dans `MediaFile`, `Midwife` et `Service`** ✅
Corrigé : tous migrés en `#[Gedmo\Slug(...)]`.

**3. `@Assert\CssColor` en docblock dans `Midwife`** ✅
Corrigé : `#[Assert\CssColor]`.

**4. `CompressingImagesCommand` — logique incohérente** ✅
Corrigé : délègue à `ImageUploadService` avec ses constantes (`MAX_WIDTH=2000`, `MAX_WEIGHT=200ko`).

**5. `ImageUploadService` : thumbnails toujours en JPEG** ✅
Corrigé : thumbnails, resize et compression en WebP. PNG source → WebP avec transparence gérée.

### Moyennes

**6. Mentions légales professionnelles incomplètes** ✅
Corrigé : champs `rpps`, `adeli`, `rcpLibelle`, `rcpNumeroContrat`, `numeroOrdinal`, `siret` ajoutés à `Midwife`.

**7. Sitemap sans `<lastmod>`** ✅
Corrigé : `TimestampableEntity` ajouté sur `Midwife` et `Domain`, `<lastmod>` dans le sitemap.

**8. `robots.txt` absent** ✅
Corrigé : route `GET /robots.txt` dans `SitemapController`.

**9. `PropertyInfoExtractor` avec la classe `Type` deprecated**
À surveiller pour Symfony 8 — non corrigé (pas de rupture en 7.4).

### Faibles (code mort / cosmétique)

**10. `Tools` service — utilité discutable** ✅
Supprimé. `MakeAdminController` (qui l'utilisait) supprimé également.

**11. `MediaFile::$slug` — champ non utilisé côté app** ✅
Supprimé : propriété, mapping ORM et `getSlug()` retirés.

---

## Axes d'amélioration possibles

### SEO (haute valeur)

**Données structurées JSON-LD** ✅ (partiellement)
- `MedicalOrganization` pour le cabinet : ✅ corrigé (`MedicalBusiness` → `MedicalOrganization`)
- `Physician` enrichi avec identifiants légaux (RPPS, ADELI, SIRET) : ✅ corrigé
- `MedicalSpecialty` pour les domaines : non traité
- `FAQPage` : non applicable (page info non structurée Q/R)

**Open Graph et Twitter Cards**
Balises `og:type`, `og:title`, `og:description`, `og:url` présentes dans `base.html.twig`. `og:image` à renseigner par page (non traité).

**`<link rel="canonical">`**
Présent dynamiquement dans `base.html.twig` via `app.request.uri`. ✅

**Core Web Vitals** ✅ (partiellement)
- `loading="lazy"` ajouté sur toutes les images non-hero dans les templates + `MediaExtension`
- WebP à l'upload : ✅ corrigé dans `ImageUploadService`
- `width`/`height` explicites : non traité (images dynamiques, dimensions inconnues à la compilation)

### Fonctionnel

**Formulaire de contact** — non traité (remplacé par les liens Doctolib, décision client)

**Page 404 personnalisée** ✅
Corrigée : liens Doctolib par sage-femme, liste des domaines, message d'exception brut supprimé.

**Gestion des fichiers côté admin (`MediaFileController`)** ✅
Créé : liste en grille avec recherche, édition `alt`/`title`/`description`, suppression avec effacement disque.

**Soft delete pour les entités** ✅
`SoftDeleteableEntity` + `#[Gedmo\SoftDeleteable]` sur `Midwife`, `Domain`, `Service`.

### Technique

**WebP à l'upload** ✅
Upload, resize, compression et thumbnails en WebP dans `ImageUploadService`.

**Tests d'intégration sur les controllers**
Non traité — à planifier dans une étape dédiée.

**Cache HTTP sur les pages front** ✅
`#[Cache(public: true, maxage: 3600, mustRevalidate: true)]` sur les 4 controllers front.

**Matomo en mode cookieless** ✅
Script ajouté dans `base.html.twig` (prod uniquement, `disableCookies()`). Configurer `MATOMO_URL` et `MATOMO_SITE_ID` dans `.env.local`.

**Gestion des images AVIF**
Non traité — WebP suffisant pour l'usage actuel, AVIF reporté.

---

## Priorisation suggérée

| Priorité | Action | Impact | Effort | Statut |
|---|---|---|---|---|
| 1 | Mentions légales professionnelles (RPPS, ADELI...) dans `Midwife` | Légal / conformité | Moyen | ✅ |
| 2 | `robots.txt` + `<lastmod>` dans sitemap | SEO immédiat | Faible | ✅ |
| 3 | `CompressingImagesCommand` — corriger la logique | Qualité / prod | Faible | ✅ |
| 4 | Formulaire de contact | Conversion | Moyen | — (Doctolib) |
| 5 | JSON-LD `MedicalOrganization` + `Physician` | SEO rich snippets | Moyen | ✅ |
| 6 | `MediaFileController` admin (liste + édition alt/title) | UX admin | Moyen | ✅ |
| 7 | WebP à l'upload | Performance / SEO | Moyen | ✅ |
| 8 | Tests d'intégration controllers front | Qualité | Moyen | — |
| 9 | `HomePageController` — null check + nettoyage | Robustesse | Faible | ✅ |
| 10 | Gedmo Slug → attributs PHP 8 | Cohérence | Faible | ✅ |

---

## Note sur le déploiement (o2switch)

o2switch est un hébergement mutualisé PHP — **pas de Docker en production**. L'environnement Docker est donc uniquement pour le développement local. Pour la production :
- Configurer les variables d'environnement via le panneau o2switch ou un `.env.local` non versionné.
- `composer dump-env prod` pour optimiser le chargement des variables.
- Vérifier que PHP 8.4 est disponible sur o2switch (certains plans sont encore sur 8.1/8.2 — demander l'activation via le support).
- AssetMapper génère les assets en statique (`bin/console asset-map:compile`) — à intégrer dans le process de déploiement.
- La commande `importmap:install` du `composer.json` doit être exécutée après chaque `composer install` en production.
