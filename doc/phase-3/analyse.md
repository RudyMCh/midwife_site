# Phase 3 — Corrections visuelles, aide photo, fixtures blog

_Analyse réalisée le 2026-04-12 — à partir de l'état du projet après la phase 2 (terminée)_

---

## Contexte

La phase 2 est terminée : design system en place, charte graphique appliquée, blog fonctionnel, aide SEO dans l'admin, accessibilité et performance Lighthouse améliorées.

La phase 3 couvre deux types de travail :
1. **Corrections visuelles** identifiées à l'usage sur les composants existants
2. **Outillage photo** — guide et validation au bon choix de format/dimensions dans l'admin
3. **Données de test** — fixtures pour les articles du blog
4. **Éléments reportés** de la phase 2

---

## Analyse des problèmes identifiés

### P1 — Carte sage-femme (homepage) : photo bulle + titre

**Constat :** La photo ronde (`.midwife-card-picture`) est positionnée en absolu à `top: 12%` avec `transform: translate(-50%, -10%)`. Elle chevauche la zone `-top` (25% de 600px = 150px) qui contient le `h3` avec le nom. Résultat : la bulle couvre partiellement ou totalement le nom.

**Root cause :**
- Position absolue non coordonnée avec la hauteur fixe de la zone `-top`
- Le `h3` est dans `-top` (25%) mais la photo circle déborde par-dessus

**Template :** [midwife-card.html.twig](../../templates/front/components/midwife-card.html.twig)
**Style :** [_midwife-card.scss](../../assets/styles/front/_midwife-card.scss) — `.midwife-card-picture { top: 12%; transform: translate(-50%, -10%) }`

---

### P2 — Carte sage-femme : section inférieure (texte + image de fond)

**Constat :** `.midwife-card-main` affiche un `aboutMe` en texte blanc sur une image de fond (`bgCard`) avec un `overlay-light` (rgba 0,0,0,0.3). Le texte est difficile à lire (contraste insuffisant avec l'overlay light), et l'image de fond est peu visible (trop sombre). On cumule les inconvénients des deux approches sans en avoir les avantages.

**Root cause :**
- Overlay `.overlay-light` à 0.3 = trop sombre pour laisser voir l'image, pas assez pour garantir la lisibilité du texte
- Pas de fallback si `bgCard` est absent
- Font-size 22px italic bold en fond coloré fragile selon la photo uploadée

**Template :** [midwife-card.html.twig](../../templates/front/components/midwife-card.html.twig) ligne 11
**Style :** [_midwife-card.scss](../../assets/styles/front/_midwife-card.scss) — `.midwife-card-main`

---

### P3 — Bouton Doctolib footer : logo SVG + texte simultanément visibles

**Constat :** Dans [doctolib-mini.html.twig](../../templates/front/components/doctolib-mini.html.twig), le lien a la classe `.btn-doctolib-mini`, et contient `<span class="btn-doctolib-mini-content">Prendre rendez-vous</span>`. Or dans `_doctolib.scss`, `.btn-doctolib-mini` applique `background: url('...svg') center/cover` sur le conteneur (75×35px) — le SVG Doctolib est ainsi affiché en grand fond, et le texte du span est visible par-dessus, créant un rendu illisible.

**Root cause :**
- Le composant `doctolib-mini` mélange deux approches incompatibles : fond SVG pleine taille + texte visible
- Manque d'une convention claire entre les deux composants Doctolib (`doctolib.html.twig` vs `doctolib-mini.html.twig`)

**Template :** [doctolib-mini.html.twig](../../templates/front/components/doctolib-mini.html.twig)
**Style :** [_doctolib.scss](../../assets/styles/front/_doctolib.scss) — `.btn-doctolib-mini`

---

### P4 — Bannière topbar (titlebar) : photos mal rendues

**Constat :** La `titlebar` (`.title`) utilise `background: center / cover no-repeat` avec une image uploadée via l'admin. Si l'image uploadée n'est pas au bon format (large paysage, rapport 16:9 minimum), le résultat est pixelisé (image trop petite agrandie), ou la photo est grossie au point de ne plus être reconnaissable.

**Root cause :**
- Aucune indication de format dans les formulaires admin
- `ImageUploadService` convertit en WebP mais ne redimensionne pas selon le contexte d'usage
- Pas de validation côté serveur ni côté client sur les dimensions minimales

**Champ concerné :** champ `bgTitle` de `MidwifeType` et autres entités utilisant la titlebar

---

### P5 — Aide au bon choix de photo (tous champs upload)

**Constat :** Aucun formulaire admin ne précise les dimensions attendues pour les photos uploadées. Chaque champ a un usage différent (photo de profil ≠ bannière ≠ image de fond de carte) et les formats optimaux varient.

**Exemples de champs concernés :**
| Champ | Usage | Format conseillé |
|---|---|---|
| `picture` (Midwife) | Photo profil, bulle ronde | Carré 400×400 min, portrait 1:1 |
| `bgTitle` (Midwife) | Bannière héros topbar | Paysage 1920×600 min, 16:5 |
| `bgCard` (Midwife) | Fond de carte sage-femme | Carré / portrait 800×800 min |
| `featuredImage` (Article) | Vignette article blog | Paysage 1200×630, 16:9 (og:image) |
| `image` (HomePage) | Carousel homepage | Paysage 1920×800 min |

**Approche souhaitée :**
- Affichage des dimensions conseillées sous le champ
- Avertissement JS si les dimensions de l'image choisie sont insuffisantes (avec message explicatif clair)
- Interdiction (blocage côté client + serveur) si les dimensions sont clairement incompatibles (ex : image carrée pour une bannière)

---

### P6 — Fixtures blog absentes

**Constat :** Aucun fichier de fixtures n'existe pour l'entité `Article`. Les développeurs et les démonstrations nécessitent des données représentatives pour tester le rendu du blog (liste, pagination, articles publiés/brouillons).

**Fichiers existants :** `AppFixtures.php`, `DomainFixtures.php`, `MidwifeFixtures.php`, `HomePageFixtures.php`, etc. — pas d'`ArticleFixtures.php`.

---

### P7 — Bug navigation desktop : items « Nos prestations » perdent leur texte

**Constat :** Dans le menu desktop, au survol de "Nos prestations", un sous-menu apparaît avec les domaines. En survolant un domaine, ses services apparaissent à droite. Quand la souris passe d'un domaine au suivant, le domaine immédiatement en dessous perd son texte (les lettres deviennent invisibles).

**Root cause probable :**
Le CSS positionne les éléments de 3ᵉ niveau (services) avec `top: -60px; left: 250px;` par rapport à leur `li` parent. Ces éléments ont `background-color: $color-gray`. Le bloc de services d'un domaine chevauche ainsi le `li` du domaine suivant — son fond gris couvre le lien du domaine suivant qui, lui, a une couleur de texte blanche sur fond gris = invisibilité.

**Style :** [_header.scss](../../assets/styles/front/_header.scss) — règle `ul ul li ul li { top: -60px; left: 250px; }`

---

## Éléments reportés de la phase 2

| Réf | Description | Pourquoi reporté |
|---|---|---|
| H3 | Tests d'intégration controllers front | Priorité fonctionnelle |
| J5 | PurgeCSS Bootstrap/FA (~300 Ko CSS) | Chantier architectural |
| E4 | `og:image` width/height (dimensions dynamiques) | Nécessite champ BDD |

---

## Todo list

> Légende : `[ ]` à faire — `[x]` fait — `[-]` décidé : non traité

### A. Corrections visuelles composants

- [x] **A1** — Carte sage-femme : photo bulle déplacée dans `.midwife-card-top` (flex column), suppression de `position: absolute` / `top` / `left` / `transform` — le nom est toujours visible sous la photo
- [x] **A2** — Carte sage-femme section inférieure : Option B retenue — overlay `rgba(0,0,0,0.55)` via `.midwife-card-overlay` (z-index 1), `p` en `position: relative; z-index: 2` ; guard `{% if midwife.bgCard %}` ajouté dans le template
- [x] **A3** — Bouton Doctolib footer : approche logo SVG seul retenue — span texte supprimé, `aria-label="Prendre rendez-vous sur Doctolib"` sur le `<a>`, `rel="noopener noreferrer"` ajouté
- [x] **A4** — Bug nav desktop "Nos prestations" : règle `ul ul li ul li { top/left }` remplacée par `ul ul li > ul { top: 0; left: 250px }` — le sous-menu de niveau 3 est repositionné sans écraser les items du niveau 2

### B. Aide photo dans l'admin

- [x] **B1** — Option `image_hint` ajoutée dans `MediaFileType` : tableau `['label', 'width', 'height', 'ratio']` — attrs `data-img-min-width`/`data-img-min-height` posés sur le `<input>`, hint textuel dans le template via `buildView`, contrainte Symfony `Image(minWidth, minHeight)` ajoutée sur `new_file` pour validation serveur (B3 couvert)
- [x] **B2** — `image-upload-hint.js` créé : `FileReader` + `Image` → dimensions réelles lues à la sélection ; avertissement (`--warning`) si dimensions insuffisantes, blocage (`--danger` + `data-img-blocked`) si < 50 % du minimum ; soumission du formulaire bloquée côté client si flag présent
- [x] **B3** — Validation serveur via contrainte `Symfony\Component\Validator\Constraints\Image` (minWidth/minHeight) posée sur le sous-champ `new_file` dans `MediaFileType` — `FormError` explicite rendu dans `media_file_theme.html.twig` via `form_errors(form.new_file)`
- [x] **B4** — Hints appliqués : `picture` (400×400, 1:1), `bgCard` (800×800), `bgTitle` (1920×600, 16:5), `pictureSelf` (600×800, 3:4) dans `MidwifeType` ; `featuredImage` (1200×630, 16:9) dans `ArticleType` ; `titleBg` (1920×600), `backgroundImage1/2` (1200×400), carousel `pictures` (1920×800 et 1200×800) dans `HomePageType` ; `titleBg` (1920×600) dans `DomainType` ; `picture` (1200×500, 12:5) dans `ServiceType`

### C. Fixtures blog

- [x] **C1** — Créer `ArticleFixtures.php` avec :
  - 8 à 10 articles variés (publiés et brouillons)
  - Auteurs issus de `MidwifeFixtures` (référence via `$this->getReference`)
  - Contenu riche (plusieurs paragraphes, listes, titres — adapté au TinyMCE)
  - Dates de publication étalées sur les 6 derniers mois
  - `metaTitle` et `metaDescription` renseignés sur les articles publiés
  - `slug` auto-généré (Gedmo Sluggable)
  - Couverture de la pagination (>9 articles pour tester la page 2)
- [x] **C2** — Déclarer la dépendance `ArticleFixtures → MidwifeFixtures` via `getDependencies()`

### D. Reportés phase 2

- [ ] **D1** — **H3** Tests d'intégration : 2-3 tests fonctionnels sur `BlogController` (liste, détail, slug inexistant → 404) et `MidwifeController` (show) — utiliser `WebTestCase` Symfony
- [-] **D2** — **J5** PurgeCSS Bootstrap/FA : reporté phase 4, nécessite outillage dédié
- [-] **D3** — **E4** `og:image` width/height : reporté, nécessite champ BDD supplémentaire

---

## Priorisation

| Priorité | Tâche | Pourquoi |
|---|---|---|
| 1 | A4 | Bug fonctionnel visible (nav cassée) |
| 2 | A3 | Composant Doctolib footer cassé visuellement |
| 3 | A1, A2 | Refonte carte sage-femme (rendu actuel insuffisant) |
| 4 | C1, C2 | Fixtures blog (nécessaires pour tester le blog) |
| 5 | B1, B2 | Aide photo JS côté client (impact UX admin immédiat) |
| 6 | B3, B4 | Validation serveur + application tous champs |
| 7 | D1 | Tests intégration (qualité code) |
