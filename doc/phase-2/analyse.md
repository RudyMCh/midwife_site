# Phase 2 — Analyse frontend et plan de travail

_Analyse réalisée le 2026-04-10 — à partir du code tel qu'il existe après la phase 1 (migration technique complète)_

---

## Contexte

La phase 1 est terminée : PHP 8.4, Symfony 7.4, AssetMapper, Vanilla JS, PHPStan level 8.
La phase 2 couvre les objectifs suivants (voir `CLAUDE.md`) :
1. Visuel et style
2. SEO — invitation à la prise de rendez-vous
3. Blog
4. Aide à l'écriture SEO dans l'admin

Cette analyse porte sur l'état actuel du frontend (templates, SCSS, JS) pour identifier ce qui doit être corrigé ou construit avant, pendant, et après ces objectifs.

---

## Analyse de l'existant

### Templates Twig

**Points forts :**
- Hiérarchie claire : `base.html.twig` → `layout.html.twig` → pages → composants
- Composants réutilisables bien découpés (`midwife-card`, `doctolib`, `service-description`...)
- JSON-LD présent (homepage + fiche sage-femme)
- Open Graph structuré dans `base.html.twig`
- `loading="lazy"` en place sur les images

**Problèmes identifiés :**
- `{% block og_image %}{% endblock %}` existe mais **aucune page ne l'implémente** : partage social vide sur toutes les pages
- Hiérarchie heading incohérente : saut h1→h4 sur `domain.html.twig`
- Alt text fallback à `''` (vide) au lieu d'une description contextuelle
- Cartes sage-femme dans des `<div>` sans `<article>` ni structure de liste
- Navs sans `aria-label` pour les distinguer (desktop vs mobile)
- `aria-expanded`/`aria-controls` absents côté HTML sur les éléments interactifs (footer contact, burger menu), bien que le JS les gère
- `<i class="fas ...">` sans `aria-hidden="true"` sur les icônes décoratives

### SCSS

**Points forts :**
- Architecture modulaire (un fichier par composant)
- Flexbox et CSS Grid modernes
- Transitions cohérentes (0.3–0.5s)
- Breakpoints principaux à 768px / 992px

**Problèmes identifiés :**

**Absence de design system :**
`_variables.scss` ne contient que 2 entrées dans une map Bootstrap (`#232187`, rose). Tout le reste est codé en dur partout dans les fichiers.

Exemples de couleurs non-tokénisées relevées :
- `color: purple` (`_header.scss`)
- `color: steelblue` (admin)
- `background-color: grey` (`_header.scss`)
- `background: linear-gradient(rgb(88, 241, 115), rgb(3, 187, 41))` (`_midwife-horizontal-card.scss`)
- `color: #232187` répété dans 8+ fichiers
- `color: rgb(253, 63, 146)` répété dans 5+ fichiers

**Z-index non systématisés :**
- `z-index: 2`, `z-index: 100`, `z-index: 120`, `z-index: 500`, `z-index: 999`, `z-index: 1001`
Aucune stratégie. Conflits inévitables lors des ajouts futurs.

**Breakpoints incohérents :**
Bootstrap utilise 576 / 768 / 992 / 1200px. Le projet mélange avec 500px et 610px custom.

**Typographie incohérente :**
`h2` et `h4` ont la même font Tangerine à 50px — la hiérarchie visuelle ne correspond pas à la hiérarchie sémantique. Font-size des headings non systématisée.

**Duplication :**
`ul { list-style: none; margin: 0; padding: 0; }` déclaré 5+ fois.
Styles flex sur les cartes (`midwife-card` / `midwife-horizontal-card`) dupliqués.

**Images de fond en inline style :**
`style="background-image: url('...')"` dans plusieurs templates → impossible à optimiser, pas de fallback couleur si image manquante.

**Contraste couleurs :**
Non vérifié (WCAG AA = ratio 4.5:1 minimum). `--bs-link-color: #adb5bd` (gris clair) sur fond sombre potentiellement insuffisant.

### JavaScript

**Points forts :**
- Vanilla JS ES6+ : `querySelector`, `addEventListener`, modules `import/export`
- IntersectionObserver pour le sticky header et les animations scroll
- Classes bien structurées (`Burger`, `ShowContactFooter`, `backgroundMenu`)
- Tom Select et TinyMCE initialisés proprement dans `admin/main.js`

**Problèmes identifiés :**

- `setTimeout(200)` dans `main.js` avant l'init des modules : patch de timing sans commentaire, comportement fragile
- `console.log()` non supprimés dans `burger.js` et `imagePreviewCompress.js` (fuite en prod)
- `imagePreviewCompress.js` utilise `XMLHttpRequest` — API du navigateur de 2010, à remplacer par `fetch()`
- Aucune gestion d'erreur réseau dans `imagePreviewCompress.js`
- Icônes FontAwesome chargées **depuis un CDN externe** (jsdelivr) : Single Point of Failure — si jsdelivr est indisponible, tout le site perd ses icônes

### Importmap / AssetMapper

- FontAwesome 6.7.2 en CDN jsdelivr (hors importmap)
- TinyMCE chargé depuis `public/tinymce/` (statique, non versionné)
- Bootstrap, Tom Select, Popper correctement dans l'importmap

---

## Ce qui manque pour la phase 2

### Prérequis à tout chantier visuel
Sans design tokens, toute modification de couleur ou de typo nécessite de toucher 10+ fichiers. C'est le socle à poser en premier.

### Prérequis SEO/social
`og:image` non implémenté = partage sur les réseaux sans image. Correction rapide, impact direct.

### Prérequis accessibilité
En France, le RGAA s'applique aux sites de professionnels de santé. Les lacunes identifiées (ARIA, contrastes, headings) doivent être corrigées.

---

## Todo list

> Légende : `[ ]` à faire — `[x]` fait — `[-]` décidé : non traité

### A. Fondations (prérequis à tout le reste)

- [x] **A1** — Créer un design system SCSS complet dans `_variables.scss` :
  - Palette couleurs (primary, secondary, dark, light, success, danger, neutral)
  - Échelle de spacing (base 4px ou 8px)
  - Hiérarchie typographique (font-families, font-sizes h1→h6, line-heights)
  - Z-index stratégiques (dropdown, sticky, modal, overlay)
  - Breakpoints officiels (supprimer les 500px/610px non standard)
  - Border-radius, box-shadow standards
- [x] **A2** — Remplacer toutes les couleurs hardcodées dans les fichiers SCSS par les variables
- [x] **A3** — Supprimer les duplications SCSS — bouton téléphone extrait en `.btn-phone` partagé (`@extend`) entre `_midwife-card.scss` et `_midwife-horizontal-card.scss`; import `variables` ajouté dans `admin/_main.scss`
- [x] **A4** — Self-host FontAwesome : `importmap:require @fortawesome/fontawesome-free/css/all.min.css` (FA 7.2.0), webfonts dans `assets/vendor/`, importé dans `app.js` et `admin.js`, CDN retiré des deux templates. `assets/vendor/` gitignorée, re-téléchargée via `importmap:install` (scripts composer).

### B. Corrections HTML/accessibilité

- [x] **B1** — `og:image` implémenté sur homepage, midwife, domain (image de fond) ; fallback `apple-touch-icon` dans `base.html.twig` ; `informationUtiles` hérite du fallback
- [x] **B2** — `twitter:card` (summary_large_image) et `twitter:image` ajoutés dans `base.html.twig` ; overrides dans homepage, midwife, domain
- [x] **B3** — Hiérarchie heading corrigée : h4→h3 dans les cartes domaine (homepage, midwife, midwife-horizontal-card) + SCSS mis à jour (`_domains-card`, `_homepage`, `_midwife-horizontal-card`)
- [x] **B4** — `aria-label="Navigation principale"` et `aria-label="Navigation mobile"` ajoutés sur les deux `<nav>` du header
- [x] **B5** — `role="button"` + `aria-expanded="false"` + `aria-controls="menu-burger-nav"` sur le toggle burger ; `role="button"` + `aria-expanded="false"` sur les 3 `.js-openLevel` ; `id="menu-burger-nav"` sur la nav burger
- [x] **B6** — `aria-hidden="true"` ajouté sur toutes les icônes FA décoratives (header, footer, midwife-card, midwife, homepage)
- [x] **B7** — Alt fallbacks contextuels : "Photo du cabinet" (carousel homepage), "Photo de Prénom Nom" (carousel midwife + pictureSelf)
- [x] **B8** — `<div>` → `<article>` sur `midwife-card.html.twig` et `midwife-horizontal-card.html.twig`
- [x] **B9** — Audit prod Lighthouse a révélé `color-contrast` sur nav sticky active link : `$color-accent` (#5a8c6a) sur `$color-bg-warm` (#fafaf8) = 3.84:1 < 4.5:1 → corrigé : `$color-primary` (#3a6b4e) = 5.9:1 ✓ dans `_header.scss`. Autres couleurs personnalisées à vérifier manuellement si nécessaire.
- [x] **B10** — `:focus-visible` global ajouté dans `_main.scss` : outline `$color-primary` 2px offset 3px sur tous les éléments interactifs (a, button, input…) ; `:focus:not(:focus-visible)` sans outline pour ne pas pénaliser les utilisateurs souris

### C. Corrections JavaScript

- [x] **C1** — `console.log()` supprimés dans `imagePreviewCompress.js` (×4) et `_bgMenu.js` (×1) ; `burger.js` était déjà propre
- [x] **C2** — `XMLHttpRequest` remplacé par `fetch()` async/await dans `imagePreviewCompress.js` ; gestion d'erreur HTTP + réseau via try/catch
- [x] **C3** — `setTimeout(200)` supprimé dans `main.js` : redondant avec `DOMContentLoaded`, aucune dépendance asynchrone réelle identifiée
- [x] **C4** — Icônes burger déjà couvertes par B6 (`aria-hidden="true"`) ; `aria-expanded` mis à jour dynamiquement dans `burger.js` (ouverture/fermeture du menu principal et des sous-niveaux)

### D. Visuel et style (objectif 1)

- [x] **D1** — Recherche et document de références visuelles : voir `doc/phase-2/charte-graphique.md`
- [x] **D2** — Charte graphique définie : Option B (vert forêt / brun-ocre / blanc cassé) — palette, polices, règles Tangerine documentées
- [x] **D3** — Charte appliquée : `_variables.scss` (nouvelle palette + `$font-heading`), `_fonts.scss` (Playfair Display + Source Sans 3, une requête), `_main.scss` (bg-warm, `h3-h6` → Playfair), `_footer.scss` + `footer.html.twig` (fond vert nuit, couleurs variables)
- [x] **D4** — Page sage-femme révisée : section contact reconstruite (`.btn-phone` direct), séparateurs path/degree corrigés (`-` → `—`/`–`), image avec `border-radius`, `_midwife.scss` responsive ajouté
- [x] **D5** — Carte horizontale (`_midwife-horizontal-card.scss`) : fond `$color-bg-light`, bordure subtile, ombre, hover vert forêt, photo avec bague blanche et ombre ; h3 couleur `$color-primary`
- [x] **D6** — `scrollReveal.js` (IntersectionObserver) + import dans `main.js` ; `_slide-in.scss` : `.module` fade+translateY avec stagger, respecte `prefers-reduced-motion`
- [x] **D7** — Header : `.header-brand` (nom du cabinet, apparaît en sticky) + layout flex container ; sticky state → `$color-bg-warm` + `box-shadow` au lieu de white opaque ; liens sticky → `$color-dark` ; Footer : h5 titles héritent `$font-heading` (Playfair) via règle globale
- [x] **D8** — `_service.scss` : padding description réduit sur mobile (`$bp-md`) ; `_homepage.scss` : carousel hauteur 260px sur mobile ; `_midwife.scss` : marges et paddings réduits sur mobile

### E. SEO — invitation à la prise de rendez-vous (objectif 2)

- [x] **E1** — `rel="noopener noreferrer"` + `aria-label` ajoutés sur tous les boutons Doctolib (`doctolib.html.twig`, `service-description.html.twig`)
- [x] **E2** — JSON-LD `MedicalWebPage` ajouté dans `domain.html.twig` (specialty Midwifery, about = liste des services, provider = cabinet)
- [x] **E3** — Boutons partage Facebook + WhatsApp ajoutés sur la fiche sage-femme (`midwife.html.twig`) ; styles dans `_midwife.scss`
- [-] **E4** — Dimensions `og:image` non ajoutées : les images sont dynamiques (admin), dimensions inconnues au rendu ; le fallback `apple-touch-icon` a déjà 180×180. Nécessiterait un champ `width`/`height` en base → reporté
- [x] **E5** — Canonical corrigé : `app.request.uri` → `app.request.schemeAndHttpHost ~ app.request.pathInfo` (query params exclus)
- [x] **E6** — Audit Lighthouse complet documenté dans `doc/phase-2/lighthouse.md` : scores desktop initial (Perf 66, Access. 70), desktop après corrections I/J (Perf 94, Access. 89), premier audit mobile (Perf 69, Access. 82)

### F. Blog (objectif 3)

- [x] **F1** — Entité `Article` (title, slug Gedmo, content, excerpt, publishedAt, author:Midwife, featuredImage:MediaFile, isPublished, metaTitle, metaDescription) ; TimestampableEntity + SoftDeleteableEntity
- [x] **F2** — Migration Doctrine `Version20260411000001` créée manuellement (driver absent en dev)
- [x] **F3** — `BlogController` (front) : liste paginée 9/page (`/blog/`) + détail (`/blog/{slug}`)
- [x] **F4** — `AdminBlogController` : CRUD complet (`/admin/blog/`) — brouillon/publié via `isPublished`
- [x] **F5** — `ArticleType` avec TinyMCE sur `content`, `MediaFileType` pour `featuredImage`, helpers SEO, `EntityType` auteure
- [x] **F6** — `ArticleHandler` : new/edit (auto-set `publishedAt` si manquant), delete CSRF
- [x] **F7** — Templates front : `blog/index.html.twig`, `blog/show.html.twig`, composant `article-card.html.twig` ; template admin `admin/blog/_form.html.twig` avec sidebar aperçu
- [x] **F8** — Soft delete via `SoftDeleteableEntity` (identique aux autres entités)
- [-] **F9** — Système activable/désactivable : le flag `isPublished` sur chaque article joue ce rôle ; un flag global app reporté (non demandé explicitement)
- [x] **F10** — Articles publiés intégrés dans `SitemapController` (page liste + chaque article)
- [-] **F11** — Ping Google : reporté — Google Search Console suffit pour un cabinet de petite taille
- [x] **F12** — `MediaFileType` + `ImageUploadService` utilisés via `featuredImage` dans `ArticleType`
- [x] **F13** — 9 tests unitaires sur `ArticleHandler` (new/edit/delete, CSRF, publishedAt auto) — 100% pass
- [x] **F14** — Lien « Blog » ajouté dans la navigation principale (desktop + burger mobile)
- [x] **F15** — Lien « Blog » ajouté dans la sidebar admin

### G. Aide à l'écriture SEO dans l'admin (objectif 4)

- [x] **G1** — Champs SEO identifiés : `metaTitle` + `metaDescription` (ArticleType, DomainType, HomePageType, InformationPageType, MidwifeType) ; `alt` (MediaFileMetaType) ; `description` (ServiceType)
- [x] **G2** — `help` contextuel ajouté sur chaque champ SEO dans les 7 form types : libellé normalisé "Titre SEO (meta title)" / "Description SEO (meta description)", placeholder avec exemple réaliste, aide décrivant la règle et le format attendu
- [x] **G3** — Page "Guide SEO" créée (`/admin/guide-seo`, `GuideController`) : explication du SEO, règles + tableau d'exemples (≥ 3) pour meta title, meta description, alt image, description prestation ; bonnes pratiques générales
- [x] **G4** — `seo-counter.js` : compteur de caractères dynamique sur tous les champs portant `data-seo-min` / `data-seo-max` ; importé dans `main.js`
- [x] **G5** — Indicateur couleur dans `.seo-counter` : vert (plage idéale), orange (hors plage mais toléré), rouge (trop court ou trop long) ; styles dans `_global.scss`
- [x] **G6** — Lien "Guide SEO" ajouté dans la sidebar admin ; conseils inline via `help` Symfony sur chaque champ

### H. Non traités (décisions à prendre)

- [-] **H1** — AVIF : WebP en place, AVIF reporté
- [-] **H2** — `width`/`height` explicites sur les images dynamiques (dimensions inconnues à la compilation)
- [ ] **H3** — Tests d'intégration sur les controllers front (reporté depuis phase 1)

### I. Accessibilité — corrections issues de l'audit Lighthouse

_Voir `doc/phase-2/lighthouse.md` pour le détail des diagnostics._

- [x] **I1** — Supprimer `maximum-scale=1` et `user-scalable=0` du `<meta name="viewport">` (`base.html.twig`)
- [x] **I2** — Contraste footer bottom corrigé : `.footer-bottom-inner` passe de `$color-footer-muted` à `$color-footer-link` (ratio 8.65:1, WCAG AA ✓)
- [x] **I3** — `aria-label="Voir la fiche de Prénom Nom"` ajouté sur `a.midwife-card-picture` (`midwife-card.html.twig`)
- [x] **I4** — `aria-label="Ouvrir dans Waze"` ajouté sur le lien Waze (`homepage.html.twig`)
- [x] **I5** — `aria-label="Ouvrir le menu"` sur `div#menu-icon` ; mis à jour dynamiquement ("Fermer le menu") dans `burger.js`
- [x] **I6** — `title="Plan d'accès au cabinet"` ajouté sur l'`<iframe>` Google Maps (`homepage.html.twig`)
- [x] **I7** — `<hr>` dans `<ul>` remplacé par `<li class="nav-separator" role="separator"><hr></li>` ; style dans `_header.scss`
- [x] **I8** — Footer h5→h4 puis **h4→h3** (audit prod a révélé un saut h2→h4 : dernier heading du contenu homepage est h2, footer sautait à h4) ; séquence finale h1→h2→h3 ✓ ; `.footer-section-title` utilise un sélecteur de classe, rendu inchangé

### J. Performance — gains immédiats issus de l'audit Lighthouse

- [x] **J1** — `<link rel="preload" as="image" fetchpriority="high">` ajouté dans `{% block preload %}` de `homepage.html.twig` ; bloc vide dans `base.html.twig`
- [x] **J2** — Google Fonts sorti du `@import` SCSS (render-blocking tardif) → `<link rel="preconnect">` + `<link rel="stylesheet">` dans `base.html.twig` ; `display=swap` déjà présent dans l'URL
- [-] **J3** — FA `font-display: swap` : impact mesuré à 5ms dans le rapport — trop marginal pour re-déclarer tous les `@font-face` vendorisés
- [x] **J4** — `symfonycasts/sass-bundle ^0.9.0` applique automatiquement `--style=compressed` quand `APP_ENV=prod` : CSS minifié sans configuration supplémentaire. Vérifié : `symfonycasts_sass.yaml` ne surcharge pas le style. Les scores Lighthouse prod (K1/L1) ont été obtenus avec `APP_DEBUG=0`, confirmant les assets minifiés (Desktop 97–98/100)
- [-] **J5** — PurgeCSS / allègement Bootstrap + FA : gain ~300 Ko CSS — chantier architectural, reporté phase 3

### K. SEO — vérification prod

- [x] **K1** — Audit prod réalisé (APP_DEBUG=0) : Desktop 98/100, Mobile 72/100, SEO 100/100 ✓ — documenté dans `doc/phase-2/lighthouse.md`. **Note : audit sans photos uploadées → score perf mobile optimiste.**
- [x] **K2** — SEO 100/100 en prod confirme l'absence de `X-Robots-Tag: noindex` (Symfony n'active plus `disallow_search_engine_index` avec `APP_DEBUG=0`)

### L. Images — impact à mesurer

> Audit prod réalisé sans photos uploadées. Ces points sont à traiter dès que le contenu photo est en place.

- [x] **L1** — Audit avec photos réelles : Desktop 97/100, Mobile 66/100, LCP mobile 5.8 s (−6 pts vs sans photos, dû au réseau simulé + CSS, pas aux images)
- [x] **L2** — `ImageUploadService` convertit automatiquement en WebP (confirmé sur audit réseau) ; images ≤ 64 Ko — aucune action nécessaire
- [x] **L3** — `{% block preload %}` ajouté dans `midwife.html.twig` (preload `bgTitle` si présent) et `blog/show.html.twig` (preload `featuredImage` si présent). `blog/index.html.twig` non traité : hero CSS-only (`background-color: $color-primary`), pas d'image à précharger

---

## Priorisation

| Priorité | Tâche | Statut | Pourquoi |
|---|---|---|---|
| 1 | A1, A2, A3 | ✓ fait | Design system — socle de tout chantier visuel |
| 2 | A4 | ✓ fait | Fiabilité (CDN externe supprimé) |
| 3 | B1, B2 | ✓ fait | SEO/social immédiat, peu d'effort |
| 4 | C1, C2, C3 | ✓ fait | Qualité code, prod-ready |
| 5 | B3–B8 | ✓ fait | Accessibilité (RGAA) |
| 6 | D1–D8 | ✓ fait | Visuel et style |
| 7 | E1–E6 | ✓ fait | SEO approfondi |
| 8 | F1–F15 | ✓ fait | Blog (fonctionnalité longue) |
| 9 | G1–G6 | ✓ fait | Aide à l'écriture SEO |
| 10 | I1–I8 | ✓ fait | Accessibilité Lighthouse (+19 pts desktop) |
| 11 | J1–J2 | ✓ fait | Performance Lighthouse (+28 pts desktop, LCP 5.6s→1.2s) |
| 12 | K1, K2 | ✓ fait | Audit prod (Desktop 98, Mobile 72, SEO 100) — sans photos |
| 13 | L1, L2 | ✓ fait | Audit avec photos (Desktop 97, Mobile 66) — WebP confirmé natif |
| 14 | B10 | ✓ fait | Focus visible clavier (`:focus-visible` global) |
| 15 | J4 | ✓ fait | Minification CSS prod confirmée (sass-bundle `--style=compressed`) |
| 16 | L3 | ✓ fait | Preload LCP étendu fiche sage-femme + article blog |
| — | **Phase 2 terminée** ✓ | | |
| — | **Reporté phase 3** | | |
| 18 | H3 | reporté phase 3 | Tests intégration |
| 19 | J5 | reporté phase 3 | PurgeCSS Bootstrap/FA (~300 Ko gain, gain majeur mobile) |
