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
- [ ] **B9** — Vérifier les ratios de contraste WCAG AA sur les couleurs principales (outil : https://webaim.org/resources/contrastchecker/)
- [ ] **B10** — Vérifier le focus visible sur tous les éléments interactifs (input, button, a)

### C. Corrections JavaScript

- [ ] **C1** — Supprimer tous les `console.log()` dans `burger.js` et `imagePreviewCompress.js`
- [ ] **C2** — Remplacer `XMLHttpRequest` par `fetch()` dans `imagePreviewCompress.js` avec gestion d'erreur
- [ ] **C3** — Documenter ou éliminer le `setTimeout(200)` dans `main.js` (trouver la vraie cause du timing)
- [ ] **C4** — Ajouter `aria-hidden` sur les icônes dans les templates modifiés par le JS burger

### D. Visuel et style (objectif 1)

- [ ] **D1** — Recherche et document de références visuelles : couleurs, typographie, style adapté au public cible (femmes, femmes enceintes)
- [ ] **D2** — Définir la charte graphique finale (palette, polices, ton)
- [ ] **D3** — Appliquer la charte sur les composants front existants
- [ ] **D4** — Révision page sage-femme (`midwife.html.twig` + `_midwife.scss`) : équilibre texte/image, rigidité de la mise en page
- [ ] **D5** — Révision des cartes sage-femme (`midwife-card`, `midwife-horizontal-card`) : cohérence stylistique
- [ ] **D6** — Ajouter des animations CSS/JS mesurées là où elles apportent de la valeur
- [ ] **D7** — Révision footer et header (espacement, typographie, hiérarchie visuelle)
- [ ] **D8** — Vérifier la cohérence visuelle sur mobile (tous les breakpoints)

### E. SEO — invitation à la prise de rendez-vous (objectif 2)

- [ ] **E1** — Évaluer la mise en avant du bouton Doctolib : visibilité, position, couleur, texte d'appel
- [ ] **E2** — Vérifier et compléter le JSON-LD `MedicalSpecialty` pour les pages domaines
- [ ] **E3** — Évaluer la pertinence d'un mécanisme de partage réseaux sociaux (boutons partage fiche sage-femme)
- [ ] **E4** — Compléter `og:image` avec dimensions explicites (`og:image:width`, `og:image:height`)
- [ ] **E5** — Vérifier le `<link rel="canonical">` sur toutes les pages (actuellement `app.request.uri` — vérifier les paramètres de pagination)
- [ ] **E6** — Audit Lighthouse complet (Performance, SEO, Accessibilité, Best Practices) et documentation des scores de départ

### F. Blog (objectif 3)

- [ ] **F1** — Définir le modèle de données : entité `Article` (title, slug, content, excerpt, publishedAt, author:Midwife, categories?, picture, isPublished, metaTitle, metaDescription)
- [ ] **F2** — Migration Doctrine pour l'entité `Article`
- [ ] **F3** — Controller front `BlogController` avec liste paginée et détail article
- [ ] **F4** — Controller admin `AdminBlogController` (CRUD, brouillon/publié, archivage)
- [ ] **F5** — Form type `ArticleType` avec TinyMCE sur `content`
- [ ] **F6** — Handler `ArticleHandler` (persist/flush, gestion image)
- [ ] **F7** — Templates : liste articles, détail article, sidebar, composant carte article
- [ ] **F8** — Soft delete pour les articles archivés (`#[Gedmo\SoftDeleteable]`)
- [ ] **F9** — Système activable/désactivable (paramètre app ou flag en base)
- [ ] **F10** — Intégration dans le sitemap dynamique (`SitemapController`)
- [ ] **F11** — Ping Google à la publication d'un article (ou instruction dans la doc admin)
- [ ] **F12** — Gestion des images dans les articles (réutiliser `ImageUploadService`)
- [ ] **F13** — Tests unitaires sur `ArticleHandler` et intégration sur `BlogController`
- [ ] **F14** — Lien blog dans la navigation front (conditionnel si activé)
- [ ] **F15** — Lien blog dans la sidebar admin

### G. Aide à l'écriture SEO dans l'admin (objectif 4)

- [ ] **G1** — Identifier tous les champs éditables liés au référencement (metaTitle, metaDescription, alt des images, slug, description sage-femme, description service, description domaine...)
- [ ] **G2** — Ajouter un helper textuel contextuel sur chaque champ SEO dans les form types (Symfony `help` option)
- [ ] **G3** — Créer une page "Guide SEO" dans l'admin (route `/admin/guide-seo`) avec :
  - Explication de ce qu'est le SEO / le référencement
  - Bonnes pratiques spécifiques à ce site
  - Au moins 3 exemples par champ éditable SEO
- [ ] **G4** — Compteur de caractères dynamique sur les champs `metaTitle` (≤60 car.) et `metaDescription` (≤160 car.) en JS
- [ ] **G5** — Indicateur de qualité visuel (couleur) selon la longueur de ces champs
- [ ] **G6** — Intégrer les conseils dans la sidebar du formulaire ou en tooltip

### H. Non traités (décisions à prendre)

- [-] **H1** — AVIF : WebP en place, AVIF reporté
- [-] **H2** — `width`/`height` explicites sur les images dynamiques (dimensions inconnues à la compilation)
- [ ] **H3** — Tests d'intégration sur les controllers front (reporté depuis phase 1)

---

## Priorisation

| Priorité | Tâche | Pourquoi |
|---|---|---|
| 1 | A1, A2, A3 | Design system — socle de tout chantier visuel |
| 2 | A4 | Fiabilité (CDN externe supprimé) |
| 3 | B1, B2 | SEO/social immédiat, peu d'effort |
| 4 | C1, C2, C3 | Qualité code, prod-ready |
| 5 | B3–B10 | Accessibilité (RGAA) |
| 6 | D1–D8 | Visuel et style |
| 7 | E1–E6 | SEO approfondi |
| 8 | F1–F15 | Blog (fonctionnalité longue) |
| 9 | G1–G6 | Aide à l'écriture SEO |
| 10 | H3 | Tests intégration |
